<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use \Yajra\DataTables\Html\Builder;
use DataTables;

use App\{IPAddress,Scan,ScanPort};

class ScanController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            return DataTables::of(IPAddress::query())
            ->addColumn('actions', function (IPAddress $ip) {
                return '<div class="btn-group">
                        <a href="'.route('scan.view',$ip->id).'"><i class="fa fa-eye"></i></a>&nbsp;
                    </div>';
            })
            ->addColumn('num_scans', function(IPAddress $ip) {
                return $ip->scans->count();
            })
            ->rawColumns(['actions'])
            ->toJson();
        }

        $builder->ajax([
            'url' => route('scan.index'),
            'dataType' => "json"
        ]);

        $dt = $builder->columns([
            ['data' => 'id', 'name' => 'id', 'title' => "SID", 'visible' => false, 'searchable' => false],
            ['data' => 'address', 'name' => 'address', 'title' => 'Address'],
            ['data' => 'type', 'name' => 'type', 'title' => 'Type'],
            ['data' => 'num_scans', 'name' => 'num_scans', 'title' => '# Scans'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions']
        ]);

        return view('scan.index', compact('dt'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        return view('scan.upload');
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
        $request->session()->flash('message', 'Import was successful. Records added below.');
        return redirect(route('scan.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Builder $builder)
    {
        $ip_address = request()->ip;
        $ip_record = IPAddress::find($ip_address);

        if (request()->ajax()) {
            return DataTables::of(Scan::query()->where('ip_address_id',$ip_address))
            ->addColumn('actions', function (Scan $scan) {
                return '<div class="btn-group">
                        <a href="'.route('scan.details',$scan->id).'"><i class="fa fa-eye"></i></a>&nbsp;
                    </div>';
            })
            ->addColumn('num_scans', function(Scan $scan) {
                return $scan->ports->count();
            })
            ->addColumn('scan_date', function(Scan $scan){
                return $scan->scanDate();
            })
            ->rawColumns(['actions'])
            ->toJson();
        }

        $builder->ajax([
            'url' => route('scan.view',$ip_address),
            'dataType' => "json"
        ]);

        $dt = $builder->columns([
            ['data' => 'id', 'name' => 'id', 'title' => "SID", 'visible' => false, 'searchable' => false],
            ['data' => 'scan_date', 'name' => 'scan_date', 'title' => 'Scan Date'],
            ['data' => 'host', 'name' => 'host', 'title' => 'host'],
            ['data' => 'num_scans', 'name' => 'num_scans', 'title' => '# Ports Scanned'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions']
        ]);

        return view('scan.view',compact('ip_record','dt'));
    }

    /**
     * Show the details for the associated scan record.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function details(Builder $builder)
    {
        $scan = Scan::find(request()->scan);

        if (request()->ajax()) {
            return DataTables::of(ScanPort::query()->where('scan_id',$scan->id))
            ->addColumn('scan_info', function(ScanPort $port) {
                return $port->protocol . "/" . $port->port;
            })
            ->rawColumns(['state_reason'])
            ->toJson();
        }

        $builder->ajax([
            'url' => route('scan.details',$scan->id),
            'dataType' => "json"
        ]);

        $dt = $builder->columns([
            ['data' => 'id', 'name' => 'id', 'title' => "SID", 'visible' => false, 'searchable' => false],
            ['data' => 'scan_info', 'name' => 'scan_info', 'title' => 'Port'],
            ['data' => 'state', 'name' => 'state', 'title' => 'State'],
            ['data' => 'reason', 'name' => 'reason', 'title' => 'Reason'],
            ['data' => 'reason_ttl', 'name' => 'reason_ttl', 'title' => 'Reason TTL'],
            ['data' => 'service', 'name' => 'service', 'title' => 'Service'],
            ['data' => 'method', 'name' => 'method', 'title' => 'Method'],
            ['data' => 'conf', 'name' => 'conf', 'title' => 'Confidence']
        ]);


        return view('scan.details',compact('scan','dt'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    /**
     * Traverse attributes and return an array - probably won't need this anymore
     *
     * @param  object  $obj
     * @return array $array
     */
    public function attrToArray($object)
    {
        var_dump($object);
        exit();
        $attributesArray = array();

        foreach($object["@attributes"][0] as $k => $v) {
            $attributesArray[$k] = (string) $v;
        }

        return $attributesArray;
    }
}
