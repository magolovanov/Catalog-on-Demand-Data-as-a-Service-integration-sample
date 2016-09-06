# Catalog-on-Demand-Data-as-a-Service-integration-sample
UI and source code that illustrate how a simple integration of Catalog-on-Demand® with Data-as-a-Service providers might work

# Synopsis

UI and source code that illustrates how a simple integration of Catalog-on-Demand® with Data-as-a-Service providers might work. Thr workflow is as the following:

1. User goes to the sample Web application deployed on a site and is prompted for a Catalog-on-Demand user ID and password, and clicks "Login". 

2. The application makes ValidateUser Catalog-on-Demand® API call, authorizes the user and obtains user attributes.

3. If ValidateUser is successful, the user is prompted to select from a list of authorized publication configurations (design specifications), and clicks Next.

4. User is prompted to upload a file that is a list of SKU numbers, and clicks Next.

5. The sample Web application calls a stub process with the list of SKUs. The sample stub process (https://webservices.catalog-on-demand.com/dataAsAServiceDemoStub/processSKUs.php) returns sample data file content.

6. User is prompted for a job ID and clicks Submit.

7. The sample Web application constructs Flexible Job File™  text that uses the job ID, configuration, and product data from the stub. The title and header are hard coded.

8. The application calls Flexible Job File™ API and submits the publication job. 

9. The application polls Catalog-on-Demand® API (GetBackgroundJobProcessingState call) every 5 seconds until the job is complete. 

10. The application displays the URL to download the completed publication.

# Installation

Place dataAsAServiceDemo as a subdirectory of any Web server HTTP document root directiory. The Web server must have PHP runtime (any more or less fresh version) installed. In tthe Web browser, navjgate to the application: 
https://webservices.catalog-on-demand.com/dataAsAServiceDemo/index.php

# Sample apllication components

## UI screens

login.php: UI for the step 2 in the wiorkflow. Calls processLogin.php and processes it's oputput.

selectConfiguration.php: UI for the steps 4,5 in the wiorkflow. Calls processConfiguration.php and processes it's oputput.

submit.php: UI for the steps 4,5 in the wiorkflow. Calls processSKUs.php and processes it's oputput.

uploadSKUs.php: UI for the steps 6,7,8 in the wiorkflow. Calls processSubmit.php and processes it's oputput.

track.php: UI for the steps 9,10 in the wiorkflow. Calls processTrack.php and processes it's oputput.

## Server-side scripts

processLogin.php: makes ValidateUser Catalog-on-Demand® API call with user credentials passed to it by login,php; in case of success, remembers user data in the sesssion. Returns call status / error description to the caller.

processLogin.php: makes ValidateUser Catalog-on-Demand® API call with user credentials passed to it by login,php; in case of success, remembers user data in the sesssion. Returns call status / error description to the caller.

processConfiguration.php: remembers passed selected configuration index in the session. Returns call status to the caller.

processSKUs.php: processes SKU numbers file upload, calls the sample stub (data provider API imitation), receives the resulting data file text and remembers it in the session. Returns call status / error description to the caller.

processSubmit.php: remembers passed  job ID, builds Flexible Job File™ text, makes Flexible Job File™ API call, processes call response, remembers session ID, Catalog-on-Demand® job ID and process ID in the session. Returns call status / error description to the caller.

processTrack.php: polls Catalog-on-Demand® API (GetBackgroundJobProcessingState call). Returns call status abd results / error description to the caller.
