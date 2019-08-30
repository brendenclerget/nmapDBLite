# nmapDBLite V0.1

![enter image description here](https://user-images.githubusercontent.com/10637853/63895036-ed9ec100-c9a2-11e9-86a1-7fde356b5336.jpg)

nmapDBLite is a Laravel/Vue application using API authentication through Laravel passport. It ingests single XML nmap report files via API (multipart) or internal file upload in order to map those to a local SQLite database and make them easier to analyze.


# Installation

[Clone from GitHub or download here](https://github.com/brendenclerget/nmapDBLite)

## Prep

Setting up nmapDBLite is quick and just a few console commands. If you're not experienced with setting up Laravel, nmapDBLite uses Laravel 5.8. Mainly, since many configurations are bundled with the app, you need to ensure your server is properly configured to run a Laravel application. [More details on that here.](https://laravel.com/docs/5.8/installation#configuration) 

Specifically, pay attention to the "Public Directory" and "Directory Permissions" sections. Ignore the "Application Key" section or other commands, we will be doing that shortly.

## Installation

Follow these steps closely, and in order, to ensure smooth deployment.

 1. As mentioned above, ensure your server is configured for Laravel.

 2. Clone/download the repository.

 3. Open the .env.example file. The following values are the only ones crucial to operation: 

"APP_URL": This will be the public root of your app. For example, if you would visit your application at "http://exampleurl.com" this is what you would enter.

"DB_CONNECTION": Ensure this is set to sqlite

"MAIL_DRIVER": You can use any Laravel mail driver that is built in. This is mainly for password reset functionality. 'smtp', 's3' and 'log' are all common settings. Log will save any emails generated to a log file in the public storage system. Ensure your public storage configured if you choose this as detailed under the configuration directions from Laravel labeled ["Directory Permissions".](https://laravel.com/docs/5.8/installation#configuration)

 4. Open config/database.php. Traditionally, the app is set to access the SQLite database at database/database.sqlite. You can set this however you want, but ensure to update it in this config file if you place it elsewhere. Below is an example config from a fresh app (database_path() simple prefixes the /database path onto the location of the 'database' array key below):

![enter image description here](https://user-images.githubusercontent.com/10637853/63894216-f0001b80-c9a0-11e9-902b-dbfbb5344328.jpg)

 5. Create blank sqlite database in the location set in the config/database.php file. Then run the following commands from your project root:

 6. php artisan key:generate
 7. php artisan migrate
 8. php artisan passport:install

 9. Once this is done, go into web.php and remove the following highlighted code to enable registration so you can create a user:
 
![enter image description here](https://user-images.githubusercontent.com/10637853/63894438-7d437000-c9a1-11e9-93ef-1f7c258e08fd.jpg)

Once you've registered a user, put that back in so that nobody else can register. You can create unlimited API keys in the backend if you only need one user to manage your keys and share a login, otherwise, register all the accounts you need, then re-enable the registration block.

 10. Login, click your name in the top right, then click "API Keys". Click Create New Token and a Vue component will popup to accept the name for the key. Once you click Create, it will store the key, and show you ONLY ONCE. If you lose this key, you will need to delete it, and create a new one.

At this point, you're ready to ingest files into the app.

 

## Ingesting nmap scans

There are two options for getting data into the application to browse.

 1. You can submit a POST request, as multipart form data, with the key 'scan' and the xml file attached. The endpoint for this request is http://yourapp.url/api/scan. Attach your API Token as a Bearer token.
 2. In the dashboard, you can click "Upload Scan" and select the xml nmap output file to import it.

The API will return a 201 with success: true if the submission is proper. Else, it will notify you properly of what went wrong.

## Viewing Scans

Click "Browse Scans" at the top and browse away to your heart's content. The app includes smart search datatables to live filter the URLs.


## Why XML File Format?

https://nmap.org/book/output-formats-output-to-database.html - The nmap docs themselves suggest this format as the best, and out of the three provided to me from nmap outputs, this is by far the easiest to manipulate/parse and least resource intensive to work with. You can also easily switch the database driver to MySQL easily, and should perform well with very large scans, or once the database becomes more populated.

## Assumptions

This package assumes you have a basic working knowledge of using the console and composer. You don't need PHP/Laravel experience as the package is ready to go with minor configuration changes. I also assumed that this will be an internal tool, so some of the user interfaces are built as such, especially the API key system. There are certain security concerns, mainly the registration form, and some CORS interface additions possibly to manage origins/clients.

I also made the assumption that anybody using this will be somewhat technical, since they're uploading security audits, and that they can follow semi-technical directions.

## Thoughts

This was a really fun project to write, and allowed me to learn a bit about the different parts of an nmap scan, what the fields mean, and why they're being looked at. I did a bit of research into why the scans are run, and it is very intense what goes into security audits, especially with this being only one small piece of the puzzle. All in all, 10/10 on the project, great to build.

I also added a TODO section because I may continue working on this a bit, as it seems that many people would be open to using it and there’s not much out there. I would need to gather a bunch of nmap scans to further test the data mapping, however.

I was getting close to the deadline however after being swamped at my job the last two days, so I didn't get a chance to write unit tests unfortunately. That's my only regret.


## TODO
 - Write unit tests
 - User management built into the application
 - Action logging
 - Manipulation of records from datatables
 - API logging
 - Accept more file types to parse into system
 - Statistical analysis of scan metrics
 - Improved dashboard functionality (Specifically, Vue datatables)

