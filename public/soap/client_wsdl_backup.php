<?php
/**
 * SOAP Client for Contact API using WSDL mode
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$result = null;
$error = null;
$action = isset($_POST['action']) ? $_POST['action'] : '';
$wsdlUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/contact-api/public/soap/server.php?wsdl';

// Initialize SOAP Client with WSDL
try {
    $client = new SoapClient($wsdlUrl, [
        'trace' => true,
        'exceptions' => true,
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
} catch (SoapFault $e) {
    $error = "SOAP Client initialization error: " . $e->getMessage();
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    try {
        switch ($action) {
            // User Operations
            case 'createUser':
                $params = [
                    'user' => [
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'username' => $_POST['username'] ?? ''
                    ]
                ];
                $result = $client->createUser($params);
                break;
                
            case 'getUser':
                $params = ['id' => (int)$_POST['user_id']];
                $result = $client->getUser($params);
                break;
                
            // Contact Operations
            case 'createContact':
                $params = [
                    'contact' => [
                        'user_id' => (int)$_POST['user_id'],
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'] ?? '',
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'] ?? ''
                    ]
                ];
                $result = $client->createContact($params);
                break;
                
            case 'getContact':
                $params = ['id' => (int)$_POST['contact_id']];
                $result = $client->getContact($params);
                break;
                
            case 'updateContact':
                $params = [
                    'contact' => [
                        'id' => (int)$_POST['contact_id'],
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'] ?? '',
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'] ?? ''
                    ]
                ];
                $result = $client->updateContact($params);
                break;
                
            case 'deleteContact':
                $params = ['id' => (int)$_POST['contact_id']];
                $result = $client->deleteContact($params);
                break;
                
            case 'getAllContacts':
                $params = ['user_id' => (int)$_POST['user_id']];
                $result = $client->getAllContacts($params);
                break;
                
            // Address Operations
            case 'createAddress':
                $params = [
                    'address' => [
                        'contact_id' => (int)$_POST['contact_id'],
                        'street' => $_POST['street'],
                        'city' => $_POST['city'],
                        'province' => $_POST['province'] ?? '',
                        'country' => $_POST['country'] ?? '',
                        'postal_code' => $_POST['postal_code'] ?? ''
                    ]
                ];
                $result = $client->createAddress($params);
                break;
                
            case 'getAddress':
                $params = ['id' => (int)$_POST['address_id']];
                $result = $client->getAddress($params);
                break;
                
            case 'updateAddress':
                $params = [
                    'address' => [
                        'id' => (int)$_POST['address_id'],
                        'street' => $_POST['street'],
                        'city' => $_POST['city'],
                        'province' => $_POST['province'] ?? '',
                        'country' => $_POST['country'] ?? '',
                        'postal_code' => $_POST['postal_code'] ?? ''
                    ]
                ];
                $result = $client->updateAddress($params);
                break;
                
            case 'deleteAddress':
                $params = ['id' => (int)$_POST['address_id']];
                $result = $client->deleteAddress($params);
                break;
                
            case 'getContactAddresses':
                $params = ['contact_id' => (int)$_POST['contact_id']];
                $result = $client->getContactAddresses($params);
                break;
        }
        
        // Get request and response for debugging
        $lastRequest = $client->__getLastRequest();
        $lastResponse = $client->__getLastResponse();
        
    } catch (SoapFault $e) {
        $error = "SOAP Error: " . $e->getMessage();
        $lastRequest = $client->__getLastRequest();
        $lastResponse = $client->__getLastResponse();
    }
}

// Helper function to display result
function displayResult($result) {
    if (is_object($result) || is_array($result)) {
        echo '<pre>' . htmlspecialchars(print_r($result, true)) . '</pre>';
    } else {
        echo htmlspecialchars($result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact API SOAP Client (WSDL Mode)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f5f5f5;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f4fc;
            border-left: 5px solid #3498db;
        }
        .error {
            background-color: #fee;
            border-left: 5px solid #e74c3c;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            background-color: #f1f1f1;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            background-color: #3498db;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .debug {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .debug pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 300px;
            overflow-y: auto;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 4px;
        }
        small {
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact API SOAP Client (WSDL Mode)</h1>
        <p>This client uses WSDL mode to interact with the SOAP server.</p>
        
        <?php if ($error): ?>
            <div class="result error">
                <h3>Error</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($result): ?>
            <div class="result">
                <h3>Result</h3>
                <?php displayResult($result); ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="openTab(event, 'user')">User</div>
            <div class="tab" onclick="openTab(event, 'contact')">Contact</div>
            <div class="tab" onclick="openTab(event, 'address')">Address</div>
        </div>
        
        <!-- User Tab -->
        <div id="user" class="tab-content active">
            <div class="section">
                <h2>Create User</h2>
                <form method="post">
                    <input type="hidden" name="action" value="createUser">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username">
                    </div>
                    <button type="submit">Create User</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Get User</h2>
                <form method="post">
                    <input type="hidden" name="action" value="getUser">
                    <div class="form-group">
                        <label for="user_id">User ID:</label>
                        <input type="number" id="user_id" name="user_id" required>
                    </div>
                    <button type="submit">Get User</button>
                </form>
            </div>
        </div>
        
        <!-- Contact Tab -->
        <div id="contact" class="tab-content">
            <div class="section">
                <h2>Create Contact</h2>
                <form method="post">
                    <input type="hidden" name="action" value="createContact">
                    <div class="form-group">
                        <label for="user_id">User ID:</label>
                        <input type="number" id="user_id" name="user_id" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <button type="submit">Create Contact</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Get Contact</h2>
                <form method="post">
                    <input type="hidden" name="action" value="getContact">
                    <div class="form-group">
                        <label for="contact_id">Contact ID:</label>
                        <input type="number" id="contact_id" name="contact_id" required>
                    </div>
                    <button type="submit">Get Contact</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Update Contact</h2>
                <form method="post">
                    <input type="hidden" name="action" value="updateContact">
                    <div class="form-group">
                        <label for="contact_id">Contact ID:</label>
                        <input type="number" id="contact_id" name="contact_id" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <button type="submit">Update Contact</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Delete Contact</h2>
                <form method="post">
                    <input type="hidden" name="action" value="deleteContact">
                    <div class="form-group">
                        <label for="contact_id">Contact ID:</label>
                        <input type="number" id="contact_id" name="contact_id" required>
                    </div>
                    <button type="submit">Delete Contact</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Get All Contacts</h2>
                <form method="post">
                    <input type="hidden" name="action" value="getAllContacts">
                    <div class="form-group">
                        <label for="user_id">User ID:</label>
                        <input type="number" id="user_id" name="user_id" required>
                    </div>
                    <button type="submit">Get All Contacts</button>
                </form>
            </div>
        </div>
        
        <!-- Address Tab -->
        <div id="address" class="tab-content">
            <div class="section">
                <h2>Create Address</h2>
                <form method="post">
                    <input type="hidden" name="action" value="createAddress">
                    <div class="form-group">
                        <label for="contact_id">Contact ID:</label>
                        <input type="number" id="contact_id" name="contact_id" required>
                    </div>
                    <div class="form-group">
                        <label for="street">Street:</label>
                        <input type="text" id="street" name="street" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Province:</label>
                        <input type="text" id="province" name="province">
                    </div>
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" id="country" name="country">
                    </div>
                    <div class="form-group">
                        <label for="postal_code">Postal Code:</label>
                        <input type="text" id="postal_code" name="postal_code">
                    </div>
                    <button type="submit">Create Address</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Get Address</h2>
                <form method="post">
                    <input type="hidden" name="action" value="getAddress">
                    <div class="form-group">
                        <label for="address_id">Address ID:</label>
                        <input type="number" id="address_id" name="address_id" required>
                    </div>
                    <button type="submit">Get Address</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Update Address</h2>
                <form method="post">
                    <input type="hidden" name="action" value="updateAddress">
                    <div class="form-group">
                        <label for="address_id">Address ID:</label>
                        <input type="number" id="address_id" name="address_id" required>
                    </div>
                    <div class="form-group">
                        <label for="street">Street:</label>
                        <input type="text" id="street" name="street" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Province:</label>
                        <input type="text" id="province" name="province">
                    </div>
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" id="country" name="country">
                    </div>
                    <div class="form-group">
                        <label for="postal_code">Postal Code:</label>
                        <input type="text" id="postal_code" name="postal_code">
                    </div>
                    <button type="submit">Update Address</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Delete Address</h2>
                <form method="post">
                    <input type="hidden" name="action" value="deleteAddress">
                    <div class="form-group">
                        <label for="address_id">Address ID:</label>
                        <input type="number" id="address_id" name="address_id" required>
                    </div>
                    <button type="submit">Delete Address</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Get Contact Addresses</h2>
                <form method="post">
                    <input type="hidden" name="action" value="getContactAddresses">
                    <div class="form-group">
                        <label for="contact_id">Contact ID:</label>
                        <input type="number" id="contact_id" name="contact_id" required>
                    </div>
                    <button type="submit">Get Contact Addresses</button>
                </form>
            </div>
        </div>
        
        <?php if (isset($lastRequest) && isset($lastResponse)): ?>
            <div class="debug">
                <h3>Debug Information</h3>
                <h4>Last Request</h4>
                <pre><?php echo htmlspecialchars($lastRequest); ?></pre>
                <h4>Last Response</h4>
                <pre><?php echo htmlspecialchars($lastResponse); ?></pre>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            // Hide all tab content
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].className = tabcontent[i].className.replace(" active", "");
            }
            
            // Remove active class from all tabs
            tablinks = document.getElementsByClassName("tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            
            // Show the current tab and add active class
            document.getElementById(tabName).className += " active";
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>
