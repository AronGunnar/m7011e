# m7011e

Setup
1. Install XAMPP<br>
    Download XAMPP.<br>
    Install and open XAMPP Control Panel.<br>

2. Start Apache and MySQL<br>
    In XAMPP, click Start next to Apache and MySQL.<br>

3. Download the Project<br>
    Clone or download the repository.<br>
    git clone https://github.com/AronGunnar/m7011e<br>
    Place the project folder in "C:/xampp/htdocs/".<br>

4. Download composer and firebase jwt token<br>
    download composer<br>
    run the command "composer require firebase/php-jwt" in your terminal<br>
    You should get a firebase and composer folder in the repository, updating PATH might also be required depening on the machine.<br>

5. Access database data<br>
    Open your browser and go to:<br>
    http://localhost:8080/your-project-folder/view_all_data.php (or whatever your Apache port is)<br>
    (this is only to view the tables when using postman or similar)<br>
    

6. Postman<br>
    create your own postman workspace and use the api endpoints from the git repository.<br>
