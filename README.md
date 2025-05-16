# notes-app-api

This is a RESTful API that manages notes. It can add, edit, delete, and show notes. It is based on [notes-app-back-end](https://github.com/gabrield-droid/notes-app-back-end).

### Warning:
The current configuration allows CORS for all origins, which is insecure for production environments. You could implement authentication or configure the CORS securely yourself as workarounds.

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
The `id`, `createdAt`, and `updatedAt` properties are managed by the server.
The other properties are input by the client.

## Requirements:
1. PHP with a minimum version 8.1.2.
   You can install it using this command on the terminal:
   ```bash
   sudo apt install php-common php-cli libapache2-mod-php php-mysql
   ```
   Installing libapache2-mod-php also installs the `Apache2 HTTP Server`.

2. Apache2 HTTP Server
   
   If you have installed `PHP` using the command in step 1 above, Apache2 HTTP Server should already be installed. Otherwise, you can install it or check whether it is installed with this command:
   ```bash
   sudo apt install apache2
   ```
3. MariaDB Server with a minimum version 10.6
   
   This is the database server where the persistent data are stored. You can install it or check whether it is installed with this command:
   ```bash
   sudo apt install mariadb-server
   ```
   After that, run this security script to restrict access to the server:
   ```bash
   sudo mysql_secure_installation
   ```

4. Apache2 HTTP Server modules `mod_headers` and `mod_rewrite`.
   You can activate these modules using the following commands:
   ```bash
   sudo a2enmod headers
   sudo a2enmod rewrite
   ``` 

5. Git (for cloning this github repository). You can skip this if you would like to download the repository manually.


## Get the Repository
   Before installing, you have to clone this repository using one of the following commands in the terminal:
   ```bash
   git clone https://github.com/gabrield-droid/notes-app-api.git
   ```
   ```bash
   git clone git@github.com:gabrield-droid/notes-app-api.git
   ```
   ```bash
   gh repo clone gabrield-droid/notes-app-api
   ```
   Alternatively, you can download the ZIP file of the repository and extract it manually.

   Place the repository folder into this directory `/var/www/`.


## Installation

### Guided Installation (Debian-based distros only)

#### This script helps set up:
- MySQL/MariaDB configuration
- Apache2 VirtualHost configuration

This guided setup does not configure a local domain (e. g. `notes-app-api.local`) or HTTPS.
It's recommended to run this guided setup first. You may later customise configurations manually as needed.

#### This method assumes:
- You are using a Debian-based distro (e.g., Debian, Ubuntu, Linux Mint)
- The above requirements are already installed.
- Your MySQL/MariaDB server is running in the `localhost`

#### Steps:
1. Open your terminal and navigate to the project directory.
2. Execute the installation script as the root user:
    ```bash
    sudo ./INSTALL_Debian.sh
    ```
    or
    ```bash
    sudo bash ./INSTALL_Debian.sh
    ```
3. Follow the on-screen instruction.

### Manual Installation
1. Configure the MySQL/MariaDB database and user credentials
   
   Open MariaDB/MySQL by running this command on the terminal:
   ```bash
   sudo mysql
   ```
   Inside the MySQL/MariaDB run these command:
   ```sql
   -- Substitute database_name, database_user, and database_password with the values you want.

   -- Create the database
   CREATE DATABASE IF NOT EXISTS `database_name`;

   -- Select the database
   USE `database_name`;
   
   -- Create the notes table
   CREATE TABLE IF NOT EXISTS `notes` (
   `id` CHAR(16) PRIMARY KEY,
   `title` VARCHAR(255) DEFAULT NULL,
   `body` TEXT DEFAULT NULL,
   `tags` VARCHAR(255) DEFAULT NULL,
   `createdAt` CHAR(24) NOT NULL,
   `updatedAt` CHAR(24) DEFAULT NULL
   );

   -- Create the MySQL/MariaDB user credentials
   CREATE USER IF NOT EXISTS 'database_user'@'localhost' IDENTIFIED BY 'database_password';

   -- Grant specific privileges to the user
   GRANT SELECT, INSERT, UPDATE, DELETE ON `database_name`.`notes` TO 'database_user'@'localhost';
   ```
   
2. Create `db_config.php` file

   Inside the project directory, create `db_config.php` file inside `mysql` folder:
   ```bash
   sudo touch mysql/db_config.php
   ```
   To edit the file, run this command:
   ```bash
   sudo nano mysql/db_config.php
   ```
   In the nano editor, paste the following lines:
   ```php
   <?php
      define("DB_USER", "database_user");
      define("DB_PASS", "database_password");
      define("DB_NAME", "database_name");
   ?>
   ```
   Substitute `database_user`, `database_password`, and `database_name` with the values you defined earlier in the previous step.

   To save, press `Ctrl+X`, then `Y`, and then `enter`.

3. Create the site configuration

   Make a configuration file in the directory `/etc/apache2/sites-available/`. You could name it whatever you like but it is recommended you name it as the name of the repository: `notes-app-api.conf`. To edit the file, open the terminal, go to `/etc/apache2/sites-available`, and run this command on the terminal:
   ```bash
   sudo nano notes-app-api.conf
   ```
   Replace `notes-app-api.conf` with your chosen filename if you named it differently.

   In the Nano editor, paste the following lines:
   ```apache
   <VirtualHost *:80>
	   #ServerName notes-app-api.local

	   ServerAdmin webmaster@localhost
	   DocumentRoot /var/www/notes-app-api

	   Header set Access-Control-Allow-Origin "*"
	   Header set Access-Control-Allow-Headers "*"
	   Header set Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
	   Header set Access-Control-Max-Age "300"

	   <Directory /var/www/notes-app-api>
		   RewriteEngine On
		   RewriteRule ^([A-Za-z0-9\/_-]+)$ index.php
	   </Directory>

	   ErrorLog ${APACHE_LOG_DIR}/error.log
	   CustomLog ${APACHE_LOG_DIR}/access.log combined
   </VirtualHost>

   # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
   ```
   To save, press `Ctrl+X`, then `Y`, and then `enter`.
   
4. Enable the site configuration file.

   Run the following command to enable the site
   ```bash
   sudo a2ensite notes-app-api.conf
   ```

5. Reload the Apache2 HTTP Server.

   To activate the newly enabled configuration file, you need to reload the Apache2 HTTP Server. Run the following command to reload Apache2 HTTP Server:
   ```bash
   sudo service apache2 reload
   ```

### Post-installation
   Verify if the application is running.

   Run this command below:
   ```bash
   curl -X GET http://localhost:80/notes
   ```
   If the output matches the following, then the application is running.
   ```
   {"status":"success","data":{"notes":[]}}
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
            <td>There are no notes</td>
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