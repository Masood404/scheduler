# Scheduler
[![License](https://img.shields.io/badge/License-GNU-blue.svg)](https://opensource.org/licenses/GPL-3.0)

## Project Description

Scheduler is a web application that provides scheduling capabilities to help you manage your tasks effectively. It is developed in PHP. The application integrates with various APIs, including a Weather API and OpenAI GPT, to provide additional functionality. It also has plans to implement a file sharing system in the near future.

The Scheduler application allows you to create, edit, and organize tasks, set reminders, and track your progress. It provides weather information for your location and utilizes OpenAI GPT for advanced capabilities like creating task ideas and offering any other text-based assistance.

Please note that API keys are required to use the Weather API and OpenAI GPT, but this README will guide you on obtaining them.

## Features

* [X] Weather information integration
* [X] OpenAI Chat GPT clone for additional assistance
* [ ] Task organization and tracking
* [ ] File sharing system (upcoming feature)

## Prerequisites

Before running the application, ensure you have the following installed:

* [XAMPP](https://www.apachefriends.org/)
* [Git](https://git-scm.com/downloads)
        
## Getting Started

1. Move to the htdocs directory:
   * Paths:
     * Windows: C:\xampp\htdocs
     * macOS: /Applications/XAMPP/htdocs
     * Linux: /opt/lampp/htdocs

   * With CLI from the system's root directory:

            cd path_to_your_xampp_dir/htdocs

2. Clone the repository:
   
        git clone https://github.com/Masood404/scheduler.git

3. Create the configuration file:

* Create a directory called config outside the htdocs directory exactly at the root folder of xampp.
* Inside the config directory, create a file called config.php.
* Open config.php and add the contents of config-example.txt to it.
        
**Note:** You will further be guided on how to obtain the Database credentials, API Keys, VAPID Keys and RSA keys(Public and Private Key), so please keep your config file open.

1. Start XAMPP:

* Start the XAMPP control panel and start the Apache server and the SQL Server.

2. Creating the database

* Open phpmyadmin by typing http://localhost/phpmyadmin/ into your browser.
* At the top bar click on User accounts, here you can see all the usernames, passwords and hostnames. You can edit some of these credentials however necessary but make sure to take a note of them.
* At your config.php file, add these aquired credentials to the correct keys, for example:

        "DB_Host" => "localhost",
        "DB_User" => "root",
        "DB_Password" => "",

* At the top bar click on databases, create a database called 'scheduler' and after the database is created click on 'scheduler' in the left bar.
* At the top bar click on SQL and past the following query in the box and click Go:

        CREATE TABLE `gptcontents` (
        `messageId` int(11) NOT NULL AUTO_INCREMENT,
        `id` int(11) NOT NULL,
        `message` text DEFAULT NULL,
        `response` text DEFAULT NULL,
        `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`messageId`)
        ) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE `gptinstances` (
        `id` int(11) NOT NULL,
        `currentMessage` text NOT NULL,
        `title` varchar(30) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE `tasks` (
        `id` int(11) NOT NULL,
        `startTime` int(11) NOT NULL,
        `endTime` int(11) NOT NULL,
        `title` text NOT NULL,
        `completed` tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


1. Access the application:

* Open your web browser and visit http://localhost/scheduler to access the Scheduler application.

## API Key Configuration

To retrieve weather information, you need to obtain an API key from the Weather API. Please follow the steps below:

1. Visit weatherApi.com and create an account.
2. Generate an API key following the guidelines provided by [weatherApi.com](https://www.weatherapi.com/).
3. Open the config.php file located in the config directory outside the htdocs directory.
4. Replace {WeatherAPIKey} with your Weather API key.

To utilize the OpenAI GPT functionality, you need to obtain an API key from OpenAI. Please follow the steps below:

1. Visit [openai.com](https://openai.com/) and create an account.
2. Generate an API key following the guidelines provided by [openai.com](https://openai.com/).
3. Open the config.php file located in the config directory outside the htdocs directory.
4. Replace {OpenAIAPIKey} with your OpenAI GPT API key.

**Note:** Ensure you keep your API keys secure and avoid sharing them publicly.

## VAPID Key Configuration

To generate and configure you VAPID keys. Please follow the steps bellow:

1. In a Unix shell CLI, locate the 'scheduler/includes/' directory by running the command:

        cd path/to/your/xampp/htdocs/scheduler/includes/

**Note:** Make sure to include your actual path to includes directory running this command will result in an error.

2. Run the file 'generateVapid.php' by running the following command in a Unix shell CLI:

        php generateVAPID.php

3. After running the php file, copy down the public_vapid and the private vapid from a similar output like:

        Array
        (
                [publicKey] => {Your generated public key}
                [privateKey] => {Your generated private key}
        )

**Note:** If this results in an error its probably cause your php is not set in your enviormental variables. To make it work follow the instructions for each different operating systems. [windows](https://dinocajic.medium.com/add-xampp-php-to-environment-variables-in-windows-10-af20a765b0ce), [mac and linux](https://askubuntu.com/questions/146903/make-php-recognized-as-a-command-in-terminal)

4. Paste your public and private VAPID keys in your config file:
        
        "Public_VAPID" => "{Your generated public key}",
	"Private_VAPID" => "{Your generated private key}",

## RSA Keys Configuration

The 'config.php' file looks for requires files in its directory(config/), 'private_key.pem' and 'public_key.pem'. To generate these files please run the following commands inside the 'config/' directory: 

```
openssl genrsa -out private_key.pem 2048 &&
openssl rsa -in private_key.pem -pubout -out public_key.pem
```

After this the config.php file automatically includes for these RSA keys in its return function. You have now finished setting up the configuration required by this project. If you have struggled setting these keys, feel free to express you problem by contacting me or by commenting.

## Usage

1. Navigate to the tasks section to start adding and managing your tasks.
2. Set reminders and prioritize your tasks based on importance and deadlines.
3. Utilize the OpenAI GPT feature to generate task suggestions or receive intelligent task recommendations.
4. Check the weather section to get current weather information for your location.
5. File sharing system (upcoming feature) will be available soon.

## License

This project is licensed under the GNU General Public License v3.0. See the [LICENSE](LICENSE.txt) file for details.

## Contact

If you encounter any issues, have questions, or would like to provide feedback on this project, please feel free to get in touch. You can reach out to me through the following methods:

- **GitHub Comments**: Leave a comment on the [Issues](https://github.com/Masood404/scheduler/issues) section of this repository. This is a great way to start a public discussion and engage with the community.

- **Email**: You can also contact me privately via email. My email address is available on my [GitHub profile](https://github.com/Masood404?tab=overview&from=2023-08-01&to=2023-08-20). Feel free to drop me a message with any inquiries or concerns.

I value your input and strive to improve this project based on your feedback. Don't hesitate to reach out if you need assistance, have suggestions, or just want to chat about coding. I look forward to hearing from you!

