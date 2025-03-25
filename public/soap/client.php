<?php
// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if SOAP extension is loaded
if (!extension_loaded('soap')) {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact API - SOAP Client</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #111827;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            background-image: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }
        h1, h2, h3 {
            background: linear-gradient(to right, #4F46E5, #EC4899);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-top: 30px;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .card {
            background-color: #1f2937;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }
        .error {
            color: #ef4444;
            font-weight: bold;
        }
        .steps {
            margin-top: 20px;
            line-height: 1.6;
        }
        .steps li {
            margin-bottom: 10px;
        }
        a {
            color: #4F46E5;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(to right, #4F46E5, #EC4899);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        code {
            background-color: #374151;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SOAP Extension Not Enabled</h1>
        
        <div class="card">
            <h2 class="error">Error: PHP SOAP Extension is not enabled</h2>
            <p>The SOAP extension is required to use this client. Please follow these steps to enable it:</p>
            
            <ol class="steps">
                <li>Open your php.ini file (located at <code>C:\xampp\php\php.ini</code>)</li>
                <li>Find the line <code>;extension=soap</code> and remove the semicolon at the beginning to make it <code>extension=soap</code></li>
                <li>Save the file</li>
                <li>Restart the Apache server in XAMPP Control Panel</li>
                <li>Refresh this page</li>
            </ol>
            
            <p>If you have already done these steps and still see this message, try the following:</p>
            <ol class="steps">
                <li>Make sure you edited the correct php.ini file (check with <code>php --ini</code> command)</li>
                <li>Verify that the SOAP extension is properly installed with your PHP</li>
                <li>Check the Apache error logs for any issues</li>
            </ol>
            
            <p>Alternatively, you can use our non-SOAP alternative implementation:</p>
            <a href="client-alternative.php" class="btn">Use Alternative Implementation</a>
        </div>
    </div>
</body>
</html>';
    exit;
}

// HTML header with dark theme styling to match the application's design
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact API - SOAP Client</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #111827;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            background-image: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2, h3 {
            background: linear-gradient(to right, #4F46E5, #EC4899);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-top: 30px;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .card {
            background-color: #1f2937;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }
        pre {
            background-color: #374151;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            color: #10b981;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #d1d5db;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #4b5563;
            border-radius: 5px;
            background-color: #374151;
            color: #e2e8f0;
        }
        button {
            background: linear-gradient(to right, #4F46E5, #EC4899);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        button:hover {
            opacity: 0.9;
        }
        .success {
            color: #10b981;
            font-weight: bold;
        }
        .error {
            color: #ef4444;
            font-weight: bold;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #4b5563;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #1f2937;
            color: #d1d5db;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
        }
        .tab.active {
            background: linear-gradient(to right, #4F46E5, #EC4899);
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact API - SOAP Client</h1>
        
        <div class="tabs">
            <div class="tab active" data-tab="user-operations">User Operations</div>
            <div class="tab" data-tab="contact-operations">Contact Operations</div>
            <div class="tab" data-tab="address-operations">Address Operations</div>
        </div>';

// Initialize SOAP Client
try {
    $options = [
        'cache_wsdl' => 0, // WSDL_CACHE_NONE value
        'trace' => 1,
        'exceptions' => true,
        'location' => 'http://localhost/contact-api/public/soap/server.php',
        'uri' => 'http://localhost/contact-api/soap/contact'
    ];
    
    // Use non-WSDL mode to match the server
    $client = new SoapClient(null, $options);
    
    // User Operations Tab
    echo '<div class="tab-content active" id="user-operations">';
    echo '<div class="card">
            <h2>Create User</h2>
            <form method="post">
                <input type="hidden" name="action" value="createUser">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn">Create User</button>
            </form>
        </div>';
    echo '<div class="card">
            <h2>Get User</h2>
            <form method="post">
                <input type="hidden" name="action" value="getUser">
                <div class="form-group">
                    <label for="get_user_id">User ID:</label>
                    <input type="number" id="get_user_id" name="user_id" required>
                </div>
                <button type="submit">Get User</button>
            </form>
        </div>';
    echo '</div>'; // End User Operations Tab
    
    // Contact Operations Tab
    echo '<div class="tab-content" id="contact-operations">';
    
    // Create Contact Form
    echo '<div class="card">
            <h2>Create Contact</h2>
            <form method="post">
                <input type="hidden" name="action" value="createContact">
                <div class="form-group">
                    <label for="user_id">User ID:</label>
                    <input type="number" id="user_id" name="user_id" required>
                    <small style="display: block; color: #a1a1aa; margin-top: 5px;">
                        Catatan: Server akan mencoba membuat user default jika user dengan ID ini tidak ditemukan.
                    </small>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <button type="submit">Create Contact</button>
            </form>
        </div>';
    
    // Get Contact Form
    echo '<div class="card">
            <h2>Get Contact</h2>
            <form method="post">
                <input type="hidden" name="action" value="getContact">
                <div class="form-group">
                    <label for="contact_id">Contact ID:</label>
                    <input type="number" id="contact_id" name="contact_id" required>
                </div>
                <button type="submit">Get Contact</button>
            </form>
        </div>';
    
    // Update Contact Form
    echo '<div class="card">
            <h2>Update Contact</h2>
            <form method="post">
                <input type="hidden" name="action" value="updateContact">
                <div class="form-group">
                    <label for="update_contact_id">Contact ID:</label>
                    <input type="number" id="update_contact_id" name="id" required>
                </div>
                <div class="form-group">
                    <label for="update_first_name">First Name:</label>
                    <input type="text" id="update_first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="update_last_name">Last Name:</label>
                    <input type="text" id="update_last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="update_email">Email:</label>
                    <input type="email" id="update_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="update_phone">Phone:</label>
                    <input type="text" id="update_phone" name="phone" required>
                </div>
                <button type="submit">Update Contact</button>
            </form>
        </div>';
    
    // Delete Contact Form
    echo '<div class="card">
            <h2>Delete Contact</h2>
            <form method="post">
                <input type="hidden" name="action" value="deleteContact">
                <div class="form-group">
                    <label for="delete_contact_id">Contact ID:</label>
                    <input type="number" id="delete_contact_id" name="contact_id" required>
                </div>
                <button type="submit">Delete Contact</button>
            </form>
        </div>';
    
    // Get All Contacts Form
    echo '<div class="card">
            <h2>Get All Contacts</h2>
            <form method="post">
                <input type="hidden" name="action" value="getAllContacts">
                <div class="form-group">
                    <label for="all_contacts_user_id">User ID:</label>
                    <input type="number" id="all_contacts_user_id" name="user_id" required>
                </div>
                <button type="submit">Get All Contacts</button>
            </form>
        </div>';
    
    echo '</div>'; // End Contact Operations Tab
    
    // Address Operations Tab
    echo '<div class="tab-content" id="address-operations">';
    
    // Create Address Form
    echo '<div class="card">
            <h2>Create Address</h2>
            <form method="post">
                <input type="hidden" name="action" value="createAddress">
                <div class="form-group">
                    <label for="address_contact_id">Contact ID:</label>
                    <input type="number" id="address_contact_id" name="contact_id" required>
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
                    <input type="text" id="province" name="province" required>
                </div>
                <div class="form-group">
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="postal_code">Postal Code:</label>
                    <input type="text" id="postal_code" name="postal_code" required>
                </div>
                <button type="submit">Create Address</button>
            </form>
        </div>';
    
    // Get Address Form
    echo '<div class="card">
            <h2>Get Address</h2>
            <form method="post">
                <input type="hidden" name="action" value="getAddress">
                <div class="form-group">
                    <label for="get_address_id">Address ID:</label>
                    <input type="number" id="get_address_id" name="address_id" required>
                </div>
                <button type="submit">Get Address</button>
            </form>
        </div>';
    
    // Update Address Form
    echo '<div class="card">
            <h2>Update Address</h2>
            <form method="post">
                <input type="hidden" name="action" value="updateAddress">
                <div class="form-group">
                    <label for="update_address_id">Address ID:</label>
                    <input type="number" id="update_address_id" name="id" required>
                </div>
                <div class="form-group">
                    <label for="update_address_contact_id">Contact ID:</label>
                    <input type="number" id="update_address_contact_id" name="contact_id" required>
                </div>
                <div class="form-group">
                    <label for="update_street">Street:</label>
                    <input type="text" id="update_street" name="street" required>
                </div>
                <div class="form-group">
                    <label for="update_city">City:</label>
                    <input type="text" id="update_city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="update_province">Province:</label>
                    <input type="text" id="update_province" name="province" required>
                </div>
                <div class="form-group">
                    <label for="update_country">Country:</label>
                    <input type="text" id="update_country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="update_postal_code">Postal Code:</label>
                    <input type="text" id="update_postal_code" name="postal_code" required>
                </div>
                <button type="submit">Update Address</button>
            </form>
        </div>';
    
    // Delete Address Form
    echo '<div class="card">
            <h2>Delete Address</h2>
            <form method="post">
                <input type="hidden" name="action" value="deleteAddress">
                <div class="form-group">
                    <label for="delete_address_id">Address ID:</label>
                    <input type="number" id="delete_address_id" name="address_id" required>
                </div>
                <button type="submit">Delete Address</button>
            </form>
        </div>';
    
    // Get Contact Addresses Form
    echo '<div class="card">
            <h2>Get Contact Addresses</h2>
            <form method="post">
                <input type="hidden" name="action" value="getContactAddresses">
                <div class="form-group">
                    <label for="get_addresses_contact_id">Contact ID:</label>
                    <input type="number" id="get_addresses_contact_id" name="contact_id" required>
                </div>
                <button type="submit">Get Contact Addresses</button>
            </form>
        </div>';
    
    echo '</div>'; // End Address Operations Tab
    
    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $result = null;
        
        echo '<div class="card">';
        echo '<h2>SOAP Response</h2>';
        
        try {
            switch ($action) {
                // User operations
                case 'createUser':
                    $user = [
                        'name' => $_POST['name'],
                        'email' => $_POST['email']
                    ];
                    $result = $client->__soapCall('createUser', [$user]);
                    
                    // Convert stdClass to array for easier access
                    $resultArray = json_decode(json_encode($result), true);
                    
                    if (isset($resultArray['success']) && $resultArray['success']) {
                        echo '<div class="success">User created successfully! User ID: ' . $resultArray['user_id'] . '</div>';
                        echo '<p>Please use this User ID when creating contacts.</p>';
                    } else {
                        echo '<div class="error">Error: ' . ($resultArray['message'] ?? 'Unknown error') . '</div>';
                    }
                    
                    // Debug information
                    echo '<div class="card">';
                    echo '<h3>SOAP Debug Information</h3>';
                    echo '<pre>';
                    echo "SOAP Request XML\n";
                    echo htmlspecialchars($client->__getLastRequest());
                    echo "\nSOAP Response XML\n";
                    echo htmlspecialchars($client->__getLastResponse());
                    echo '</pre>';
                    echo '</div>';
                    break;
                case 'getUser':
                    $result = $client->__soapCall('getUser', [(int)$_POST['user_id']]);
                    
                    // Convert stdClass to array for easier access
                    $resultArray = json_decode(json_encode($result), true);
                    
                    if (isset($resultArray['name'])) {
                        echo '<div class="success">User found!</div>';
                        echo '<pre>' . print_r($resultArray, true) . '</pre>';
                    } else {
                        echo '<div class="error">Error: User not found</div>';
                    }
                    
                    // Debug information
                    echo '<div class="card">';
                    echo '<h3>SOAP Debug Information</h3>';
                    echo '<pre>';
                    echo "SOAP Request XML\n";
                    echo htmlspecialchars($client->__getLastRequest());
                    echo "\nSOAP Response XML\n";
                    echo htmlspecialchars($client->__getLastResponse());
                    echo '</pre>';
                    echo '</div>';
                    break;
                    
                // Contact operations
                case 'createContact':
                    $contact = [
                        'user_id' => (int)$_POST['user_id'],
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone']
                    ];
                    $result = $client->__soapCall('createContact', [$contact]);
                    
                    // Convert stdClass to array for easier access
                    $resultArray = json_decode(json_encode($result), true);
                    
                    if (isset($resultArray['success']) && $resultArray['success']) {
                        echo '<div class="success">Contact created successfully! Contact ID: ' . $resultArray['contact_id'] . '</div>';
                        echo '<p>Please use this Contact ID when creating addresses.</p>';
                    } else {
                        echo '<div class="error">Error: ' . ($resultArray['message'] ?? 'Unknown error') . '</div>';
                    }
                    
                    // Debug information
                    echo '<div class="card">';
                    echo '<h3>SOAP Debug Information</h3>';
                    echo '<pre>';
                    echo "SOAP Request XML\n";
                    echo htmlspecialchars($client->__getLastRequest());
                    echo "\nSOAP Response XML\n";
                    echo htmlspecialchars($client->__getLastResponse());
                    echo '</pre>';
                    echo '</div>';
                    break;
                    
                case 'getContact':
                    $result = $client->__soapCall('getContact', [(int)$_POST['contact_id']]);
                    break;
                    
                case 'updateContact':
                    $contact = [
                        'id' => (int)$_POST['id'],
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone']
                    ];
                    $result = $client->__soapCall('updateContact', [$contact]);
                    break;
                    
                case 'deleteContact':
                    $result = $client->__soapCall('deleteContact', [(int)$_POST['contact_id']]);
                    break;
                    
                case 'getAllContacts':
                    $result = $client->__soapCall('getAllContacts', [(int)$_POST['user_id']]);
                    break;
                    
                // Address operations
                case 'createAddress':
                    $address = [
                        'contact_id' => (int)$_POST['contact_id'],
                        'street' => $_POST['street'],
                        'city' => $_POST['city'],
                        'province' => $_POST['province'],
                        'country' => $_POST['country'],
                        'postal_code' => $_POST['postal_code']
                    ];
                    $result = $client->__soapCall('createAddress', [$address]);
                    
                    // Convert stdClass to array for easier access
                    $resultArray = json_decode(json_encode($result), true);
                    
                    if (isset($resultArray['success']) && $resultArray['success']) {
                        echo '<div class="success">Address created successfully! Address ID: ' . $resultArray['address_id'] . '</div>';
                    } else {
                        echo '<div class="error">Error: ' . ($resultArray['message'] ?? 'Unknown error') . '</div>';
                    }
                    
                    // Debug information
                    echo '<div class="card">';
                    echo '<h3>SOAP Debug Information</h3>';
                    echo '<pre>';
                    echo "SOAP Request XML\n";
                    echo htmlspecialchars($client->__getLastRequest());
                    echo "\nSOAP Response XML\n";
                    echo htmlspecialchars($client->__getLastResponse());
                    echo '</pre>';
                    echo '</div>';
                    break;
                    
                case 'getAddress':
                    $result = $client->__soapCall('getAddress', [(int)$_POST['address_id']]);
                    break;
                    
                case 'updateAddress':
                    $address = [
                        'id' => (int)$_POST['id'],
                        'contact_id' => (int)$_POST['contact_id'],
                        'street' => $_POST['street'],
                        'city' => $_POST['city'],
                        'province' => $_POST['province'],
                        'country' => $_POST['country'],
                        'postal_code' => $_POST['postal_code']
                    ];
                    $result = $client->__soapCall('updateAddress', [$address]);
                    break;
                    
                case 'deleteAddress':
                    $result = $client->__soapCall('deleteAddress', [(int)$_POST['address_id']]);
                    break;
                    
                case 'getContactAddresses':
                    $result = $client->__soapCall('getContactAddresses', [(int)$_POST['contact_id']]);
                    break;
            }
            
            // Display the result
            echo '<pre>' . print_r($result, true) . '</pre>';
            
            // Show request and response XML for debugging
            echo '<h3>SOAP Request XML</h3>';
            echo '<pre>' . htmlspecialchars($client->__getLastRequest()) . '</pre>';
            
            echo '<h3>SOAP Response XML</h3>';
            echo '<pre>' . htmlspecialchars($client->__getLastResponse()) . '</pre>';
            
        } catch (SoapFault $e) {
            echo '<div class="error">SOAP Error: ' . $e->getMessage() . '</div>';
            
            if (isset($client)) {
                echo '<h3>SOAP Request XML</h3>';
                echo '<pre>' . htmlspecialchars($client->__getLastRequest()) . '</pre>';
            }
        }
        
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="card">
            <div class="error">Error initializing SOAP client: ' . $e->getMessage() . '</div>
          </div>';
}

// JavaScript for tab functionality
echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll(".tab");
        const tabContents = document.querySelectorAll(".tab-content");
        
        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                const tabId = this.getAttribute("data-tab");
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove("active"));
                tabContents.forEach(content => content.classList.remove("active"));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add("active");
                document.getElementById(tabId).classList.add("active");
            });
        });
    });
</script>';

echo '</div></body></html>';
