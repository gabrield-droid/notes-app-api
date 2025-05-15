# notes-php-api

This is a RESTful API that manages notes. It can add, edit, delete, and show notes. It is a PHP version of [notes-app-back-end](https://github.com/gabrield-droid/notes-app-back-end).

### Warning:
This project is intended as a learning tool. This current version is not yet designed for production.

### Current Limitations:
* This API stores data only in APCu, and the data will be lost upon every restart. You could implement a persistent database as a workaround for production.
* The current configuration allows CORS for all origins, which is insecure for production environments. You could implement authentication or configure the CORS securely yourself as workarounds.
* This API has not implemented the HTTPS yet. You will need to configure the HTTPS yourself.

## Data structure:
Data example:
```json
{
   "id": "V1StGXR8_Z5jdHi6B",
   "title": "Sejarah JavaScript",
   "createdAt": "2020-12-23T23:00:09.686Z",
   "updatedAt": "2020-12-23T23:00:09.686Z",
   "tags": ["NodeJS", "JavaScript"],
   "body": "JavaScript pertama kali dikembangkan oleh Brendan Eich dari Netscape di bawah nama Mocha, yang nantinya namanya diganti menjadi LiveScript, dan akhirnya menjadi JavaScript. Navigator sebelumnya telah mendukung Java untuk lebih bisa dimanfaatkan para pemrogram yang non-Java.",
}
```
The ```id```, ```createdAt```, and ```updatedAt``` properties are managed by the server.
The other properties are input by the client.

## Requirements:
1. PHP with a minimum version 8.1.2.
   You can install it using this command on the terminal:
   ```bash
   sudo apt install php-common libapache2-mod-php php-cli
   ```
   Installing libapache2-mod-php also installs the ```Apache HTTP Server```.
2. Apache HTTP Server
   
   If you have installed ```PHP``` using the command in step 1 above, Apache HTTP Server should already be installed. Otherwise, you can install it or check whether it is installed with this command:
   ```bash
   sudo apt install apache2
   ```
3. APC User Cache
    
   APC User Cache is a PECL extension. You need to have  ```pecl``` installed on your machine to install APC User Cache. You can check if ```pecl``` is installed using this command:
   ```bash
   pecl version
   ```
   If you don't have ```pecl``` (i.e., the command yields an error), or you have just installed  ```PHP``` using step 1 above, you can install it using the following command:
   ```bash
   sudo apt install php-dev php-pear
   ```
   Then, install APC User Cache using this command:
   ```bash
   sudo pecl install apcu
   ```
   After that, add these lines to ```php.ini``` which is most likely located in ```/etc/php/[php_version]/apache2/``` (You can find the x.y php_version by running ```php -v```):
   ```
   extension=apcu.so
   apc.enabled = 1
   ```

4. Apache HTTP Server modules ```mod_headers``` and ```mod_rewrite```.
   You can activate these modules using the following commands:
   ```bash
   sudo a2enmod headers
   sudo a2enmod rewrite
   ``` 

5. Git (for cloning this github repository). You can skip this if you would like to download the repository manually.


## Installation
1. Cloning/download the github repository.
   You can clone this repository using one of the following commands in the terminal:
   ```bash
   git clone https://github.com/gabrield-droid/notes-php-api.git
   ```
   ```bash
   git clone git@github.com:gabrield-droid/notes-php-api.git
   ```
   ```bash
   gh repo clone gabrield-droid/notes-php-api
   ```
   Alternatively, you can download the ZIP file of the repository and extract it manually.

   Place the repository folder into this directory ```/var/www/```.

2. Create the site configuration
   
   Make a configuration file in the directory ```/etc/apache2/sites-available/```. You could name it whatever you like but it is recommended you name it as the name of the repository: ```notes-php-api.conf```. To edit the file, open the terminal, go to ```/etc/apache2/sites-available```, and run this command on the terminal:
   ```bash
   sudo nano notes-php-api.conf
   ```
   Replace ```notes-php-api.conf``` with your chosen filename if you named it differently.

   In the Nano editor, paste the following lines:
   ```apache
   <VirtualHost *:80>
	   # The ServerName directive sets the request scheme, hostname and port that
	   # the server uses to identify itself. This is used when creating
	   # redirection URLs. In the context of virtual hosts, the ServerName
	   # specifies what hostname must appear in the request's Host: header to
	   # match this virtual host. For the default virtual host (this file) this
	   # value is not decisive as it is used as a last resort host regardless.
	   # However, you must set it for any further virtual host explicitly.
	   #ServerName www.example.com

	   ServerAdmin webmaster@localhost
	   DocumentRoot /var/www/notes-php-api

	   Header set Access-Control-Allow-Origin "*"
	   Header set Access-Control-Allow-Headers "*"
	   Header set Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
	   Header set Access-Control-Max-Age "300"	

	   <Directory /var/www/notes-php-api>
		   RewriteEngine On
		   RewriteRule ^([A-Za-z0-9\/_-]+)$ index.php
	   </Directory>

	   # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	   # error, crit, alert, emerg.
	   # It is also possible to configure the loglevel for particular
	   # modules, e.g.
	   #LogLevel info ssl:warn

	   ErrorLog ${APACHE_LOG_DIR}/error.log
	   CustomLog ${APACHE_LOG_DIR}/access.log combined

	   # For most configuration files from conf-available/, which are
	   # enabled or disabled at a global level, it is possible to
	   # include a line for only one particular virtual host. For example the
	   # following line enables the CGI configuration for this host only
	   # after it has been globally disabled with "a2disconf".
	   #Include conf-available/serve-cgi-bin.conf
   </VirtualHost>

   # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
   ```
   To save, press ```Ctrl+X```, then ```Y```, and then ```enter```.
   
3. Enable the site configuration file.

   Run the following command to enable the site
   ```bash
   sudo a2ensite notes-php-api.conf
   ```

4. Restart the Apache HTTP Server.

   To activate the newly enabled configuration file, you need to the Apache HTTP Server. Run the following command to reload Apache HTTP Server:
   ```bash
   sudo service apache2 reload
   ```

5. Verify if the application is running.

   Run this command below:
   ```bash
   curl -X GET http://localhost:80/notes
   ```
   If the output matches the following, then the application is running.
   ```
   {"statusCode":404,"error":"Not Found","message":"Not Found"}
   ```

## Use Cases and their Request and Response Formats
### 1. Adding a note
   Request:
   * Method: **POST**
   * Endpoint: **/notes**
   * Body Request:
     ```json
     {
        "title": "Judul Catatan",
        "tags": ["Tag 1", "Tag 2"],
        "body": "Konten catatan"
     }
     ```
     
   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The request succeeds</td>
            <td><strong>201 (Created)</strong></td>
            <td>
               <pre>
                  <code>            
{
   "status": "success",
   "message": "Catatan berhasil ditambahkan",
   "data": {
   "noteid": "V09YExygSUYogwWJ"
   }
}
                  </code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The request fails</td>
            <td><strong>500 (Internal Server Error)</strong></td>
            <td>     
               <pre>
                  <code>       
{
   "status": "error",
   "message": "Catatan gagal untuk ditambahkan"
}
                  </code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 2. Showing all the notes
   Request:
   * Method: **GET**
   * Endpoint: **/notes**
  
   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>There are some notes</td>
            <td rowspan=2><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "data": {
      "notes": [
         {
            "id":"V1StGXR8_Z5jdHi6B-myT",
            "title":"Catatan 1",
            "createdAt":"2020-12-23T23:00:09.686Z",
            "updatedAt":"2020-12-23T23:00:09.686Z",
            "tags":[
               "Tag 1",
               "Tag 2"
            ],
            "body":"Isi dari catatan 1"
         },
         {
            "id":"V1StGXR8_98apmLk3mm1",
            "title":"Catatan 2",
            "createdAt":"2020-12-23T23:00:09.686Z",
            "updatedAt":"2020-12-23T23:00:09.686Z",
            "tags":[
               "Tag 1",
               "Tag 2"
            ],
            "body":"Isi dari catatan 2"
         }
      ]
   }
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>There are no books</td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "data": {
      "notes": []
   }
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 3. Showing note's details
   Request:
   * Method: **GET**
   * Endpoint: **/notes/{noteId}**

   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The note's <code>id</code> is found</td>
            <td><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "data": {
      "note": {
         "id":"V1StGXR8_Z5jdHi6B-myT",
         "title":"Catatan 1",
         "createdAt":"2020-12-23T23:00:09.686Z",
         "updatedAt":"2020-12-23T23:00:09.686Z",
         "tags":[
            "Tag 1",
            "Tag 2"
         ],
         "body":"Isi dari catatan 1"
      }
   }
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The note's <code>id</code> is not found</td>
            <td><strong>404 (Not Found)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Catatan tidak ditemukan"
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 4. Editing a note
   Request:
   * Method: **PUT**
   * Endpoint: **/notes/{noteId}**
   * Body Request:
     ```json
     {
        "title":"Judul Catatan Revisi",
        "tags":[
           "Tag 1",
           "Tag 2"
        ],
        "body":"Konten catatan"
     }
     ```
   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The note is successfully updated</td>
            <td><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "message": "Catatan berhasil diperbaharui"
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The note's <code>id</code> is not found</td>
            <td><strong>404 (Not found)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Gagal memperbarui catatan. Id catatan tidak ditemukan"
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 5. Deleting a note
   Request:
   * Method: **DELETE**
   * Endpoint: **/notes/{noteId}**

   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The note is successfully deleted</td>
            <td><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "message": "Catatan berhasil dihapus"
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The note's  <code>id</code> is not found</td>
            <td><strong>404 (Not Found)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Catatan gagal dihapus. Id catatan tidak ditemukan"
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

## Example: How to use the API using cURL
#### cURL basic syntax:
```
curl -X {HTTP METHOD} -H "Content-Type: application/json" -d {BODY REQUEST} http://localhost:80/{ENDPOINT}
```
The ```-H "Content-Type: application/json" -d``` can be omitted if there is no body request passed on the request.

#### Example 1: Adding a note
```bash
curl -X POST -H "Content-Type: application/json" -d "{\"title\": \"Judul Catatan\", \"tags\": [\"Tag 1\", \"Tag 2\"], \"body\": \"Konten Catatan\"}" http://localhost:80/notes
```

#### Example 2: Showing all note
```bash
curl -X GET http://localhost:80/notes
```