<div id="top"></div>
<div align="right">

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/84e30cbee58949cbad70ebaef13a299c)](https://www.codacy.com/gh/siakamansaly/Community_Website_with_Symfony/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=siakamansaly/Community_Website_with_Symfony&amp;utm_campaign=Badge_Grade)

</div>
<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/siakamansaly/Blog_PHP_MVC">
    <img src="public/logo.png" alt="Logo">
  </a>
  <h1 align="center">Community Website with Symfony</h1>
  <p align="center">
    SnowTricks
  </p>
</div>

<!-- ABOUT THE PROJECT -->
## About The Project

<div align="center">
    <img src="public/Screenshot.png" alt="Screenshot" width="700px">
</div>
<p>Creation of a collaborative site to promote snowboarding and help learning tricks. Project developed with Symfony</p>
<p>The project contains:</p>
<ul>
  <li>a connection and registration system</li>
  <li>a frontend part with Tricks presentation </li>
  <li>a part allowing users to comment on a Trick</li>
  <li>a backend part to manage tricks.</li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- Built With -->
## Built With

This section list the main frameworks/libraries used to start your project.
<ul>
  <li><a href="https://symfony.com/doc/5.4/index.html" target="_blank">Symfony</a></li>
  <li><a href="https://startbootstrap.com/theme/freelancer" target="_blank">Freelancer theme by StartBootstrap</a></li>
  <li><a href="https://getbootstrap.com/" target="_blank">Bootstrap</a></li>
  <li><a href="https://jquery.com" target="_blank">JQuery</a></li>
  <li><a href="https://www.php.net/" target="_blank">PHP</a></li>
  <li><a href="https://www.mysql.com/fr/">MySQL</a></li>
  <li><a href="https://twig.symfony.com/" target="_blank">Twig</a></li>
  <li><a href="https://getcomposer.org/" target="_blank">Composer</a></li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- Prerequisites -->
## Prerequisites

This is the list of things you need to use the software.
   ```sh
      - PHP: >=7
      - MySQL
      - Apache
      - Composer
   ```
<!-- GETTING STARTED -->
## Getting Started

To get a local copy up and running follow these simple example steps :

1.&nbsp;Clone the repo **Community_Website_with_Symfony**
   ```sh
   git clone https://github.com/siakamansaly/Community_Website_with_Symfony.git
   ```

2.&nbsp;Import the **BlogPerso.sql** file from the **database** folder into your SQL database (You can delete the **database** folder after installing the database).

3.&nbsp;Install composer packages
   ```sh
   cd Community_Website_with_Symfony
   composer install
   ```
4.&nbsp;Youu customize variables of file **.env** as needed to run the environment.
   ```sh
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"
   ```

5.&nbsp;Run project (Change **3000** by your local port)
   ```sh
   php -S localhost:3000 -t public/
   ```

6.&nbsp;Log in with the following administrator account :
   ```sh
   -Username : admin@example.fr
   -Password : password
   ```

7.&nbsp;Finally, change the **email** and **password** of administrator account ("My Account" section)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1.&nbsp;Fork the Project

2.&nbsp;Create your Feature Branch (`git checkout -b feature/NewFeature`)

3.&nbsp;Commit your Changes (`git commit -m 'Add some NewFeature'`)

4.&nbsp;Push to the Branch (`git push origin feature/NewFeature`)

5.&nbsp;Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTACT -->
## Contact

Siaka MANSALY : [siaka.mansaly@gmail.com](siaka.mansaly@gmail.com) 

LinkedIn : [https://www.linkedin.com/in/siaka-mansaly/](https://www.linkedin.com/in/siaka-mansaly/)

Project Link: [https://github.com/siakamansaly/Community_Website_with_Symfony](https://github.com/siakamansaly/Community_Website_with_Symfony.git)
              
<p align="right">(<a href="#top">back to top</a>)</p>

## Acknowledgments

Thanks to my mentor [Hamza](https://github.com/Hamzasakrani) for his guidance and support!

<ul>
  <li><a href="https://symfony.com/doc/current/components/http_foundation.html" target="_blank">faker</a></li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>
