# SpinShare API
## Connect API
### Introduction
Welcome to the API documentations to the SpinShare Connect API. This document outlines how to connect a SpinShare userprofile with your application and how you can use this connection to perform user actions such as creating reviews or adding charts to playlists.

### Terminology
| Name | Description |
| ---- | ----------- |
| User | A "User" is a SpinShare userprofile. |
| ConnectApp | A "ConnectApp" is your application, bot or whatever. |
| Connection | A "Connection" is a relationship between a ConnectApp and a User. A User can always revoke access to a ConnectApp by going into the usersettings on SpinShare |
| ConnectCode | A "ConnectCode" is a 6 character code that is used for establishing the connection between a User and a ConnectApp. This code will change every 15 seconds as long as the user is on the SpinShare connect page. |
| ConnectToken | A "ConnectToken" is a series of characters used along with your ApiKey to verify you are allowed to perform certain actions and ping certain api endpoints. This token is valid until the user revokes access. |

### Getting Access
API access is handled privately as of now. Please refer to the developers on the SpinShare Discord to receive API access.

### Creating a Connection
The SpinShare API does not consume login credentials for protected API endpoints but rather a simple apikey/token solution. Users have to connect to a ConnectApp once by inputting a 6 character long ConnectCode that changes every 15 seconds.

**Steps to establish a Connection**
- Prompt the user to input their connect code. The code can be found on within the profile settings under the "Connect" tab.
- Put the ConnectCode through the /getToken API endpoint along with your ApiKey to generate a ConnectToken.
- Save the ConnectToken locally and use it for protected API endpoints. If you need to verify if your ConnectToken is still valid, you can use the /validateToken API endpoint.

### API Endpoints
#### /api/connect/getToken
This API endpoint gives you a ConnectToken.

**Parameters**
(string) connectCode
(string) connectAppApiKey

**Response**
200 - OK. The body contains the ConnectToken
400 - Parameters are missing. The body contains the needed parameters and their value
404 - The ConnectCode or ApiKey was wrong.

#### /api/connect/validateToken
This API endpoint checks if a ConnectToken is still valid.

**Parameters**
(string) connectToken

**Response**
200 - The ConnectToken is valid.
404 - The ConnectToken is not valid.