<?php
/**
 * SOAP Client for Contact API
 * This client uses WSDL mode to interact with the SOAP server
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if SOAP extension is enabled
if (!extension_loaded('soap')) {
    die('<div style="color: red; font-weight: bold; padding: 20px; background-color: #ffeeee; border-radius: 5px; margin: 20px;">
        PHP SOAP extension is not enabled. Please enable it in your php.ini file by uncommenting the line "extension=soap" and restart your web server.
        <br><br>
        Alternative solution: You can use <a href="client-alternative.php">client-alternative.php</a> which doesn\'t require the SOAP extension.
    </div>');
}

// Initialize variables
$result = null;
$debug = [];
$operation = isset($_POST['operation']) ? $_POST['operation'] : '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create SOAP client with WSDL
        $wsdlUrl = 'http://localhost/contact-api/public/soap/contact.wsdl';
        $client = new SoapClient($wsdlUrl, [
            'trace' => true,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        ]);
        
        // Handle different operations
        switch ($operation) {
            // User Operations
            case 'createUser':
                $user = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'username' => $_POST['username'] ?? ''
                ];
                $result = $client->createUser(['user' => $user]);
                break;
                
            case 'getUser':
                $result = $client->getUser(['id' => (int)$_POST['id']]);
                break;
                
            // Contact Operations
            case 'createContact':
                $contact = [
                    'user_id' => (int)$_POST['user_id'],
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone']
                ];
                $result = $client->createContact(['contact' => $contact]);
                break;
                
            case 'getContact':
                $result = $client->getContact(['id' => (int)$_POST['id']]);
                break;
                
            case 'updateContact':
                $contact = [
                    'id' => (int)$_POST['id'],
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone']
                ];
                $result = $client->updateContact(['contact' => $contact]);
                break;
                
            case 'deleteContact':
                $result = $client->deleteContact(['id' => (int)$_POST['id']]);
                break;
                
            case 'getAllContacts':
                $result = $client->getAllContacts(['user_id' => (int)$_POST['user_id']]);
                break;
                
            // Address Operations
            case 'createAddress':
                $address = [
                    'contact_id' => (int)$_POST['contact_id'],
                    'street' => $_POST['street'],
                    'city' => $_POST['city'],
                    'province' => $_POST['province'],
                    'country' => $_POST['country'],
                    'postal_code' => $_POST['postal_code']
                ];
                $result = $client->createAddress(['address' => $address]);
                break;
                
            case 'getAddress':
                $result = $client->getAddress(['id' => (int)$_POST['id']]);
                break;
                
            case 'updateAddress':
                $address = [
                    'id' => (int)$_POST['id'],
                    'street' => $_POST['street'],
                    'city' => $_POST['city'],
                    'province' => $_POST['province'],
                    'country' => $_POST['country'],
                    'postal_code' => $_POST['postal_code']
                ];
                $result = $client->updateAddress(['address' => $address]);
                break;
                
            case 'deleteAddress':
                $result = $client->deleteAddress(['id' => (int)$_POST['id']]);
                break;
                
            case 'getContactAddresses':
                $result = $client->getContactAddresses(['contact_id' => (int)$_POST['contact_id']]);
                break;
        }
        
        // Collect debug information
        if ($client) {
            $debug = [
                'request' => $client->__getLastRequest(),
                'response' => $client->__getLastResponse()
            ];
        }
    } catch (SoapFault $e) {
        $result = ['error' => $e->getMessage()];
        $debug = [
            'error' => $e->getMessage(),
            'request' => isset($client) ? $client->__getLastRequest() : 'No request',
            'response' => isset($client) ? $client->__getLastResponse() : 'No response'
        ];
    } catch (Exception $e) {
        $result = ['error' => $e->getMessage()];
        $debug = [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
}

// Helper function to convert objects to arrays recursively
function objectToArray($obj) {
    if (is_object($obj)) {
        $obj = (array)$obj;
    }
    if (is_array($obj)) {
        return array_map(__FUNCTION__, $obj);
    }
    return $obj;
}

// Convert result to array for easier access
if (is_object($result)) {
    $result = objectToArray($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact API SOAP Client (WSDL Documentation)</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #f8f9fa;
            background: linear-gradient(135deg, #1a1a2e, #16213e, #1a1a2e);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2, h3 {
            color: #e2e8f0;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #4a5568;
            overflow-x: auto;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #2d3748;
            color: #cbd5e0;
            border: none;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        .tab.active {
            background: linear-gradient(135deg, #6b46c1, #805ad5);
            color: white;
        }
        .tab-content {
            display: none;
            padding: 20px;
            background-color: rgba(45, 55, 72, 0.7);
            border-radius: 0 5px 5px 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .tab-content.active {
            display: block;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #e2e8f0;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #4a5568;
            border-radius: 4px;
            background-color: #2d3748;
            color: #e2e8f0;
        }
        button {
            grid-column: span 2;
            background: linear-gradient(135deg, #6b46c1, #805ad5);
            color: white;
            border: none;
            padding: 12px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background: linear-gradient(135deg, #805ad5, #6b46c1);
        }
        .card {
            background-color: rgba(45, 55, 72, 0.7);
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        pre {
            background-color: #1a202c;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .success {
            background-color: rgba(56, 161, 105, 0.2);
            color: #68d391;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error {
            background-color: rgba(229, 62, 62, 0.2);
            color: #fc8181;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        small {
            color: #a0aec0;
            display: block;
            margin-top: 5px;
        }
        .note {
            background-color: rgba(66, 153, 225, 0.2);
            color: #90cdf4;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        @media (max-width: 768px) {
            form {
                grid-template-columns: 1fr;
            }
            button {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact API SOAP Client (WSDL Documentation)</h1>
        <h2>Gerry Moeis - 23091397164 - 2023E</h2>
        
        <div class="tabs">
            <button class="tab active" onclick="openTab(event, 'user-tab')">User Operations</button>
            <button class="tab" onclick="openTab(event, 'contact-tab')">Contact Operations</button>
            <button class="tab" onclick="openTab(event, 'address-tab')">Address Operations</button>
        </div>
        
        <!-- User Operations Tab -->
        <div id="user-tab" class="tab-content active">
            <div class="card">
                <h2>Create User</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="createUser">
                    
                    <div>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div>
                        <label for="username">Username (optional):</label>
                        <input type="text" id="username" name="username">
                    </div>
                    
                    <button type="submit">Create User</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Get User</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="getUser">
                    
                    <div>
                        <label for="user_id">User ID:</label>
                        <input type="number" id="user_id" name="id" required>
                    </div>
                    
                    <button type="submit">Get User</button>
                </form>
            </div>
        </div>
        
        <!-- Contact Operations Tab -->
        <div id="contact-tab" class="tab-content">
            <div class="card">
                <h2>Create Contact</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="createContact">
                    
                    <div>
                        <label for="contact_user_id">User ID:</label>
                        <input type="number" id="contact_user_id" name="user_id" required>
                        <small>ID dari User yang telah dibuat sebelumnya</small>
                    </div>
                    
                    <div>
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    
                    <div>
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                    
                    <div>
                        <label for="contact_email">Email:</label>
                        <input type="email" id="contact_email" name="email" required>
                    </div>
                    
                    <div>
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    
                    <button type="submit">Create Contact</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Get Contact</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="getContact">
                    
                    <div>
                        <label for="get_contact_id">Contact ID:</label>
                        <input type="number" id="get_contact_id" name="id" required>
                    </div>
                    
                    <button type="submit">Get Contact</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Update Contact</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="updateContact">
                    
                    <div>
                        <label for="update_contact_id">Contact ID:</label>
                        <input type="number" id="update_contact_id" name="id" required>
                    </div>
                    
                    <div>
                        <label for="update_first_name">First Name:</label>
                        <input type="text" id="update_first_name" name="first_name" required>
                    </div>
                    
                    <div>
                        <label for="update_last_name">Last Name:</label>
                        <input type="text" id="update_last_name" name="last_name" required>
                    </div>
                    
                    <div>
                        <label for="update_email">Email:</label>
                        <input type="email" id="update_email" name="email" required>
                    </div>
                    
                    <div>
                        <label for="update_phone">Phone:</label>
                        <input type="text" id="update_phone" name="phone" required>
                    </div>
                    
                    <button type="submit">Update Contact</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Delete Contact</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="deleteContact">
                    
                    <div>
                        <label for="delete_contact_id">Contact ID:</label>
                        <input type="number" id="delete_contact_id" name="id" required>
                    </div>
                    
                    <button type="submit">Delete Contact</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Get All Contacts for User</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="getAllContacts">
                    
                    <div>
                        <label for="get_all_user_id">User ID:</label>
                        <input type="number" id="get_all_user_id" name="user_id" required>
                    </div>
                    
                    <button type="submit">Get All Contacts</button>
                </form>
            </div>
        </div>
        
        <!-- Address Operations Tab -->
        <div id="address-tab" class="tab-content">
            <div class="card">
                <h2>Create Address</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="createAddress">
                    
                    <div>
                        <label for="address_contact_id">Contact ID:</label>
                        <input type="number" id="address_contact_id" name="contact_id" required>
                        <small>ID dari Contact yang telah dibuat sebelumnya</small>
                    </div>
                    
                    <div>
                        <label for="street">Street:</label>
                        <input type="text" id="street" name="street" required>
                    </div>
                    
                    <div>
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    
                    <div>
                        <label for="province">Province:</label>
                        <input type="text" id="province" name="province" required>
                    </div>
                    
                    <div>
                        <label for="country">Country:</label>
                        <input type="text" id="country" name="country" required>
                    </div>
                    
                    <div>
                        <label for="postal_code">Postal Code:</label>
                        <input type="text" id="postal_code" name="postal_code" required>
                    </div>
                    
                    <button type="submit">Create Address</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Get Address</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="getAddress">
                    
                    <div>
                        <label for="get_address_id">Address ID:</label>
                        <input type="number" id="get_address_id" name="id" required>
                    </div>
                    
                    <button type="submit">Get Address</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Update Address</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="updateAddress">
                    
                    <div>
                        <label for="update_address_id">Address ID:</label>
                        <input type="number" id="update_address_id" name="id" required>
                    </div>
                    
                    <div>
                        <label for="update_street">Street:</label>
                        <input type="text" id="update_street" name="street" required>
                    </div>
                    
                    <div>
                        <label for="update_city">City:</label>
                        <input type="text" id="update_city" name="city" required>
                    </div>
                    
                    <div>
                        <label for="update_province">Province:</label>
                        <input type="text" id="update_province" name="province" required>
                    </div>
                    
                    <div>
                        <label for="update_country">Country:</label>
                        <input type="text" id="update_country" name="country" required>
                    </div>
                    
                    <div>
                        <label for="update_postal_code">Postal Code:</label>
                        <input type="text" id="update_postal_code" name="postal_code" required>
                    </div>
                    
                    <button type="submit">Update Address</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Delete Address</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="deleteAddress">
                    
                    <div>
                        <label for="delete_address_id">Address ID:</label>
                        <input type="number" id="delete_address_id" name="id" required>
                    </div>
                    
                    <button type="submit">Delete Address</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Get All Addresses for Contact</h2>
                <form method="post">
                    <input type="hidden" name="operation" value="getContactAddresses">
                    
                    <div>
                        <label for="get_addresses_contact_id">Contact ID:</label>
                        <input type="number" id="get_addresses_contact_id" name="contact_id" required>
                    </div>
                    
                    <button type="submit">Get All Addresses</button>
                </form>
            </div>
        </div>
        
        <?php if ($result): ?>
        <div class="card">
            <h2>Result</h2>
            <?php if (isset($result['error'])): ?>
                <div class="error">Error: <?php echo htmlspecialchars($result['error']); ?></div>
            <?php elseif (isset($result['return']) && isset($result['return']['success'])): ?>
                <?php if ($result['return']['success']): ?>
                    <div class="success">
                        <?php echo htmlspecialchars($result['return']['message']); ?>
                        <?php if (isset($result['return']['user_id'])): ?>
                            <br>User ID: <?php echo htmlspecialchars($result['return']['user_id']); ?>
                        <?php endif; ?>
                        <?php if (isset($result['return']['contact_id'])): ?>
                            <br>Contact ID: <?php echo htmlspecialchars($result['return']['contact_id']); ?>
                        <?php endif; ?>
                        <?php if (isset($result['return']['address_id'])): ?>
                            <br>Address ID: <?php echo htmlspecialchars($result['return']['address_id']); ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="error">Error: <?php echo htmlspecialchars($result['return']['message']); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <pre><?php print_r($result); ?></pre>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($debug)): ?>
        <div class="card">
            <h3>SOAP Debug Information</h3>
            <pre>
<?php if (isset($debug['request'])): ?>
SOAP Request XML
<?php echo htmlspecialchars($debug['request']); ?>
<?php endif; ?>

<?php if (isset($debug['response'])): ?>
SOAP Response XML
<?php echo htmlspecialchars($debug['response']); ?>
<?php endif; ?>

<?php if (isset($debug['error'])): ?>
Error: <?php echo htmlspecialchars($debug['error']); ?>
<?php endif; ?>

<?php if (isset($debug['trace'])): ?>
Trace:
<?php echo htmlspecialchars($debug['trace']); ?>
<?php endif; ?>
            </pre>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function openTab(evt, tabName) {
            var i, tabContent, tabLinks;
            
            // Hide all tab content
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].classList.remove("active");
            }
            
            // Remove active class from all tab buttons
            tabLinks = document.getElementsByClassName("tab");
            for (i = 0; i < tabLinks.length; i++) {
                tabLinks[i].classList.remove("active");
            }
            
            // Show the current tab and add active class to the button
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
        
        // Set default tab
        document.addEventListener('DOMContentLoaded', function() {
            // User tab is set as default in HTML
        });
    </script>
</body>
</html>
