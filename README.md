<!-- Improved compatibility of back to top link: See: https://github.com/othneildrew/Best-README-Template/pull/73 -->
<a id="readme-top"></a>
<!--
*** Thanks for checking out the Best-README-Template. If you have a suggestion
*** that would make this better, please fork the repo and create a pull request
*** or simply open an issue with the tag "enhancement".
*** Don't forget to give the project a star!
*** Thanks again! Now go create something AMAZING! :D
-->



<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <!--<a href="https://github.com/Foxelou/WebUSBPrinter">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>-->

<h3 align="center">Web USB Printer</h3>

  <p align="center">
    A local web interface for easy printing and scanning of PDF documents from a browser, working on any device.
    <br />
    <!--<a href="https://github.com/Foxelou/WebUSBPrinter"><strong>Explore the docs »</strong></a>
    <br />-->
    <br />
    <a href="https://github.com/Foxelou/WebUSBPrinter">View Demo</a>
    &middot;
    <a href="https://github.com/Foxelou/WebUSBPrinter/issues/new?labels=bug&template=bug-report---.md">Report Bug</a>
    &middot;
    <a href="https://github.com/Foxelou/WebUSBPrinter/issues/new?labels=enhancement&template=feature-request---.md">Request Feature</a>
    <br>
    <a href="README-FR.md">README.md in French</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <!--<li><a href="#roadmap">Roadmap</a></li>-->
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

This dashboard allows users to:
- Print documents directly from the web interface
- Scan and download documents
- View recent print and scan

It's optimized for **tablets**, **smartphones**, and **desktops**, with a design that works like a **web app**: minimal scrolling, responsive layout.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With


* [![PHP][php.net]][PHP-url]
* [![HTML][html]][HTML-url]
* [![CSS][CSS]][CSS-url]
* [![PowerShell][Powershell]][Powershell-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

Follow these steps to install and run the web interface for easy printing and scanning of PDF documents in a local environment (WAMP) :

### Prerequisites

* [Wampserver](https://www.wampserver.com/)
* [Wampserver - Files and addons](https://wampserver.aviatechno.net/) (Download Microsoft VC++ x86 and x64 packages)
  * [All VC++ Redistributable Packages x86 x64 (direct download link)](https://wampserver.aviatechno.net/files/vcpackages/all_vc_redist_x86_x64.zip)
  * [Checking installed VC++ packages  (direct download link)](https://wampserver.aviatechno.net/files/tools/check_vcredist.exe)
### Installation

1. Install Microsoft VC++ x86 and x64 packages and Wampserver
2. Clone the repo in your /www/ folder. (C:\wamp64\www\ on Windows) 
   ```sh
   git clone https://github.com/Foxelou/WebUSBPrinter.git
   ```
3. Modify the config.php configuration file as you want

4. Change git remote url to avoid accidental pushes to base project
   ```sh
   git remote set-url origin github_username/repo_name
   git remote -v # confirm the changes
   ```
   ⚠️ At this stage, the site can only be accessed from your machine. It is not yet accessible to other devices on your local network.
5. Create a VirtualHost for local network access

    Open C:\wamp64\bin\apache\apache2.x.x\conf\extra\httpd-vhosts.conf

    Copy and paste this configuration :
    ```
    # Virtual Hosts
    #
    <VirtualHost *:80>
    ServerName localhost
    ServerAlias localhost
    DocumentRoot "${INSTALL_DIR}/www"
    <Directory "${INSTALL_DIR}/www/">
        Options +Indexes +Includes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    </VirtualHost>
    ```
6. Allow network connections in Apache

    Open C:\wamp64\bin\apache\apache2.x.x\conf\httpd.conf

    Check that the following lines are present and not commented (# before Listen):
    ```
    Listen 0.0.0.0:80
    Listen [::0]:80
    ```

    And replace `ServerName localhost:80` with your local ip address (example: `ServerName 192.168.1.2:80`)
6. Restart WAMP Server

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

* Access the dashboard via http://localhost/WebUSBPrinter
* Use the menu to:
  * Upload and print PDF files
  * Trigger scan and download results

[![Product Name Screen Shot][product-screenshot]](https://example.com)

<!-- _For more examples, please refer to the [Documentation](https://example.com)_-->

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ROADMAP
## Roadmap

- [ ] Feature 1
- [ ] Feature 2
- [ ] Feature 3
    - [ ] Nested Feature

See the [open issues](https://github.com/Foxelou/WebUSBPrinter/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>  -->



<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- ### Top contributors:

<a href="https://github.com/Foxelou/WebUSBPrinter/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=Foxelou/WebUSBPrinter" alt="contrib.rocks image" />
</a> -->



<!-- LICENSE -->
## License

Distributed under the MIT License. See [LICENCE](license-url) for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Portfolio : https://elouanbret.alwaysdata.net/

Project Link: [https://github.com/Foxelou/WebUSBPrinter](https://github.com/Foxelou/WebUSBPrinter)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

* [Wampserver](https://www.wampserver.com/)
* [Wampserver - Files and addons](https://wampserver.aviatechno.net/) (Download Microsoft VC++ x86 and x64 packages)
* [Sumatra PDF](https://www.sumatrapdfreader.org/free-pdf-reader)
* [Sumatra PDF - Command line arguments](https://www.sumatrapdfreader.org/docs/Command-line-arguments)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/Foxelou/WebUSBPrinter.svg?style=for-the-badge
[contributors-url]: https://github.com/Foxelou/WebUSBPrinter/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/Foxelou/WebUSBPrinter.svg?style=for-the-badge
[forks-url]: https://github.com/Foxelou/WebUSBPrinter/network/members
[stars-shield]: https://img.shields.io/github/stars/Foxelou/WebUSBPrinter.svg?style=for-the-badge
[stars-url]: https://github.com/Foxelou/WebUSBPrinter/stargazers
[issues-shield]: https://img.shields.io/github/issues/Foxelou/WebUSBPrinter.svg?style=for-the-badge
[issues-url]: https://github.com/Foxelou/WebUSBPrinter/issues
[license-shield]: https://img.shields.io/github/license/Foxelou/WebUSBPrinter.svg?style=for-the-badge
[license-url]: https://github.com/Foxelou/WebUSBPrinter/blob/master/LICENSE

[php.net]: https://img.shields.io/badge/PHP-4f5b93?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/

[html]: https://img.shields.io/badge/HTML5-f06529?style=for-the-badge&logo=html5&logoColor=white
[html-url]: https://developer.mozilla.org/en-US/docs/Web/HTML

[css]: https://img.shields.io/badge/CSS3-2965f1?style=for-the-badge&logo=css&logoColor=white
[css-url]: https://developer.mozilla.org/en-US/docs/Web/CSS

[powershell]: https://img.shields.io/badge/Powershell-1b9cf2?style=for-the-badge&logo=Powershell&logoColor=white
[powershell-url]: https://learn.microsoft.com/en-us/powershell/scripting/overview?view=powershell-7.5