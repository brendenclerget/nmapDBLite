<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

use App\{IPAddress,Scan,ScanPort};
use App\Http\Controllers\Controller;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate file
        $validatedData = $request->validate([
            'scan' => 'required|file|mimes:xml'
        ]);
        
        // Get the uploaded XML file
        $xmlFile = file_get_contents($request->scan->path());

        // Convert all the Simple XML objects to arrays so it's consistent
        $xml = json_decode(json_encode((array) simplexml_load_string($xmlFile)), 1);
        
        // Prep blank hosts array
        $newScan = array();

        // Restructure data into a bit better format
        foreach($xml['host'] as $h) {

            // Reset the ports for each result
            $portsArray = array();

            foreach($h["ports"]["port"] as $p) {
                $portsArray[] = array(
                    "protocol" => $p["@attributes"]["protocol"],
                    "port" => $p["@attributes"]["portid"],
                    "state" => $p["state"]["@attributes"]["state"],
                    "reason" => $p["state"]["@attributes"]["reason"],
                    "reason_ttl" => $p["state"]["@attributes"]["reason_ttl"],
                    "service" => $p["service"]["@attributes"]["name"],
                    "method" => $p["service"]["@attributes"]["method"],
                    "conf" => $p["service"]["@attributes"]["conf"]
                );
            };

            $newScan[] = array(
                "start" => $h["@attributes"]["starttime"],
                "end" => $h["@attributes"]["endtime"],
                "status" => $h["status"]["@attributes"],
                "ip_addr" => $h["address"]["@attributes"],
                "host" => isset($h["hostnames"]["hostname"]) ? $h["hostnames"]["hostname"]["@attributes"]["name"] : NULL,
                "host_type" => isset($h["hostnames"]["hostname"]) ? $h["hostnames"]["hostname"]["@attributes"]["type"] : NULL,
                "ports" => $portsArray,
                "times" => $h["times"]["@attributes"]
            );

        }

        // Loop through the scans and insert
        foreach($newScan as $scan) {

            // Check IP Address against db to see if it exists already, if not, create it
            $ip_record = IPAddress::firstOrCreate(
                ['address' => $scan['ip_addr']['addr']],
                ['type' => $scan['ip_addr']['addrtype']]
            );

            $scan_record = Scan::firstOrCreate(
                [
                    'ip_address_id' => $ip_record->id,
                    'start' => $scan['start'],
                    'end' => $scan['end'],
                    'srtt' => $scan['times']['srtt'],
                    'rttvar' => $scan['times']['rttvar'],
                    'to' => $scan['times']['to']
                ],
                [
                    'state' => $scan['status']['state'],
                    'reason' => $scan['status']['reason'],
                    'reason_ttl' => $scan['status']['reason_ttl'],
                    'host' => $scan['host'],
                    'host_type' => $scan['host_type']
                ]
            );
            
            foreach($scan['ports'] as $k=>$v) {
                $scan_ports = ScanPort::firstOrCreate(
                    [
                        'scan_id' => $scan_record->id, 
                        'port' => $v['port']
                    ],$v);
            }
           
        }

        return Response::json([
            'success' => 'true'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
