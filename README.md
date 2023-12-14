# Scheduler

[![License](https://img.shields.io/badge/License-GNU-blue.svg)](https://opensource.org/licenses/GPL-3.0)

## Project Description

Scheduler is a web application that provides scheduling capabilities to help you manage your tasks effectively. It is developed in PHP. The application integrates with various APIs, including a Weather API and OpenAI GPT, to provide additional functionality. It also has plans to implement a file sharing system in the near future.

The Scheduler application allows you to create, edit, and organize tasks, set reminders, and track your progress. It provides weather information for your location and utilizes OpenAI GPT for advanced capabilities like creating task ideas and offering any other text-based assistance.

Please note that API keys are required to use the Weather API and OpenAI GPT, but this README will guide you on obtaining them.

## Features

- [x] Weather information integration
- [x] OpenAI Chat GPT clone for additional assistance
- [ ] Task organization and tracking
- [ ] File sharing system (upcoming feature)

## Prerequisites

Before running the application, ensure you have the following installed:

- [XAMPP](https://www.apachefriends.org/)
- [Git](https://git-scm.com/downloads)

## Getting Started

1.  Move to the htdocs directory:

    - Paths:

      - Windows: C:\xampp\htdocs
      - macOS: /Applications/XAMPP/htdocs
      - Linux: /opt/lampp/htdocs

    - With CLI from the system's root directory:

             cd path_to_your_xampp_dir/htdocs

2.  Clone the repository:

        git clone https://github.com/Masood404/scheduler.git

3.  Start XAMPP:

- Start the XAMPP control panel and start the Apache server and the SQL Server.

4. Creating the database

- Open phpmyadmin by typing http://localhost/phpmyadmin/ into your browser.
- At the top bar click on User accounts, here you can see all the usernames, passwords and hostnames. You can edit some of these credentials however necessary but make sure to take a note of them.

- At the top bar click on databases, create a database called 'scheduler' and after the database is created click on 'scheduler' in the left bar.
- Take a note of all the aquired database credentials.

## API Key Configuration

To retrieve weather information, you need to obtain an API key from the Weather API. Please follow the steps below:

1. Visit weatherApi.com and create an account.
2. Generate an API key following the guidelines provided by [weatherApi.com](https://www.weatherapi.com/).
3. Take a note of your weather api key.

To utilize the OpenAI GPT functionality, you need to obtain an API key from OpenAI. Please follow the steps below:

1. Visit [openai.com](https://openai.com/) and create an account.
2. Generate an API key following the guidelines provided by [openai.com](https://openai.com/).
3. Take a note of your Open AI api key.

**Note:** Ensure you keep your API keys secure and avoid sharing them publicly.

## Setup

After aquiring all the necessary credentials. Locate and run setup.php inside the directory includes/setup/setup.php through cli

        php setup.php

Input the following:

1. Database host
2. Database username
3. Database password, can be left empty if the corresponding to the username.
4. Database name, the newly created database 'scheduler'.
5. Open AI API Key.
6. Weather API Key.
7. Xampp path, The path to your XAMPP directory. It differs for each operating system or XAMPP installation configuration.
8. Web Root path, The path to your XAMPP server's public root. Usually xampp/htdocs but it differs because of XAMPP installation configuration.

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
