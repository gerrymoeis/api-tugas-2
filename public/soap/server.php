<?php
/**
 * SOAP Server for Contact API
 * Implementasi sederhana tanpa bergantung pada framework Laravel
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection (MySQL)
try {
    // Baca konfigurasi dari file .env
    $envFile = __DIR__ . '/../../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0 || empty(trim($line))) {
                continue;
            }
            
            // Pisahkan key dan value, dan bersihkan komentar jika ada
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Hapus komentar pada value jika ada
                if (strpos($value, '#') !== false) {
                    $value = trim(explode('#', $value)[0]);
                }
                
                $env[$key] = $value;
            }
        }
    }

    // Gunakan konfigurasi dari .env atau default
    $dbHost = $env['DB_HOST'] ?? '127.0.0.1';
    $dbPort = $env['DB_PORT'] ?? '3306';
    $dbName = $env['DB_DATABASE'] ?? 'contact_api';
    $dbUser = $env['DB_USERNAME'] ?? 'root';
    $dbPass = $env['DB_PASSWORD'] ?? '';

    // Buat koneksi PDO ke MySQL
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Log koneksi berhasil
    error_log("Database connection successful: {$dsn}");
} catch (PDOException $e) {
    // Log error
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Contact Service class for SOAP operations
 */
class ContactService
{
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Create a new user
     * 
     * @param mixed $user User data
     * @return array Response with success status and message
     */
    public function createUser($user)
    {
        try {
            // Debug the input
            error_log('SOAP createUser input: ' . print_r($user, true));
            
            // Extract user data from parameters
            $userData = [];
            
            if (is_array($user) && isset($user['user'])) {
                $userData = $user['user'];
            } elseif (is_object($user) && isset($user->user)) {
                $userData = (array)$user->user;
            } elseif (is_array($user)) {
                $userData = $user;
            } elseif (is_object($user)) {
                $userData = (array)$user;
            }
            
            // Validate required fields
            if (empty($userData['name']) || empty($userData['email'])) {
                return ['success' => false, 'message' => 'Name and email are required'];
            }
            
            // Check if user with this username already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$userData['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'User with this email already exists'];
            }
            
            // Create the user with current timestamp
            $currentTime = date('Y-m-d H:i:s');
            $stmt = $this->pdo->prepare("INSERT INTO users (name, username, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $userData['name'],
                $userData['email'], // Gunakan email sebagai username
                password_hash('password', PASSWORD_DEFAULT), // Default password
                $currentTime,
                $currentTime
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            return [
                'success' => true, 
                'message' => 'User created successfully', 
                'user_id' => $userId
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP createUser error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param mixed $params User ID
     * @return mixed User data or error response
     */
    public function getUser($params)
    {
        try {
            // Extract ID from parameters
            $id = null;
            
            if (is_array($params) && isset($params['id'])) {
                $id = $params['id'];
            } elseif (is_object($params) && isset($params->id)) {
                $id = $params->id;
            } else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            // Get user from database
            $stmt = $this->pdo->prepare("SELECT id, name, username as email FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            return $user;
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP getUser error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving user: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create a new contact
     * 
     * @param mixed $contact Contact data
     * @return array Response with success status and message
     */
    public function createContact($contact)
    {
        try {
            // Debug the input
            error_log('SOAP createContact input: ' . print_r($contact, true));
            
            // Extract contact data from parameters
            $contactData = [];
            
            if (is_array($contact) && isset($contact['contact'])) {
                $contactData = $contact['contact'];
            } elseif (is_object($contact) && isset($contact->contact)) {
                $contactData = (array)$contact->contact;
            } elseif (is_array($contact)) {
                $contactData = $contact;
            } elseif (is_object($contact)) {
                $contactData = (array)$contact;
            }
            
            // Validate required fields
            if (empty($contactData['user_id']) || empty($contactData['first_name'])) {
                return ['success' => false, 'message' => 'User ID and first name are required'];
            }
            
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([(int)$contactData['user_id']]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Create the contact with current timestamp
            $currentTime = date('Y-m-d H:i:s');
            $stmt = $this->pdo->prepare("INSERT INTO contacts (user_id, first_name, last_name, email, phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                (int)$contactData['user_id'],
                $contactData['first_name'],
                $contactData['last_name'] ?? null,
                $contactData['email'] ?? null,
                $contactData['phone'] ?? null,
                $currentTime,
                $currentTime
            ]);
            
            $contactId = $this->pdo->lastInsertId();
            
            return [
                'success' => true, 
                'message' => 'Contact created successfully', 
                'contact_id' => $contactId
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP createContact error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error creating contact: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get contact by ID
     * 
     * @param mixed $params Contact ID
     * @return mixed Contact data or error response
     */
    public function getContact($params)
    {
        try {
            // Extract ID from parameters
            $id = null;
            
            if (is_array($params) && isset($params['id'])) {
                $id = $params['id'];
            } elseif (is_object($params) && isset($params->id)) {
                $id = $params->id;
            } else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            // Get contact from database
            $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            $contact = $stmt->fetch();
            
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            return $contact;
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP getContact error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving contact: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update a contact
     * 
     * @param mixed $params Contact data with ID
     * @return array Response with success status and message
     */
    public function updateContact($params)
    {
        try {
            // Debug the input
            error_log('SOAP updateContact input: ' . print_r($params, true));
            
            // Extract contact data from parameters
            $contactData = [];
            
            if (is_array($params) && isset($params['contact'])) {
                $contactData = $params['contact'];
            } elseif (is_object($params) && isset($params->contact)) {
                $contactData = (array)$params->contact;
            } elseif (is_array($params)) {
                $contactData = $params;
            } elseif (is_object($params)) {
                $contactData = (array)$params;
            }
            
            // Validate required fields
            if (empty($contactData['id'])) {
                return ['success' => false, 'message' => 'Contact ID is required'];
            }
            
            if (empty($contactData['user_id'])) {
                return ['success' => false, 'message' => 'User ID is required'];
            }
            
            // Check if contact exists and belongs to the user
            $stmt = $this->pdo->prepare("SELECT id FROM contacts WHERE id = ? AND user_id = ?");
            $stmt->execute([(int)$contactData['id'], (int)$contactData['user_id']]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Contact not found or does not belong to the specified user'];
            }
            
            // Build update query dynamically based on provided fields
            $updateFields = [];
            $params = [];
            
            if (isset($contactData['first_name'])) {
                $updateFields[] = "first_name = ?";
                $params[] = $contactData['first_name'];
            }
            
            if (isset($contactData['last_name'])) {
                $updateFields[] = "last_name = ?";
                $params[] = $contactData['last_name'];
            }
            
            if (isset($contactData['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $contactData['email'];
            }
            
            if (isset($contactData['phone'])) {
                $updateFields[] = "phone = ?";
                $params[] = $contactData['phone'];
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No fields to update'];
            }
            
            // Add updated_at timestamp
            $updateFields[] = "updated_at = ?";
            $params[] = date('Y-m-d H:i:s');
            
            // Add contact ID to params
            $params[] = (int)$contactData['id'];
            
            // Update the contact
            $sql = "UPDATE contacts SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true, 
                'message' => 'Contact updated successfully'
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP updateContact error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating contact: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete a contact
     * 
     * @param mixed $params Contact ID
     * @return array Response with success status and message
     */
    public function deleteContact($params)
    {
        try {
            // Extract ID from parameters
            $id = null;
            
            if (is_array($params) && isset($params['id'])) {
                $id = $params['id'];
            } elseif (is_object($params) && isset($params->id)) {
                $id = $params->id;
            } else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            // Check if contact exists
            $stmt = $this->pdo->prepare("SELECT id FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            // Delete related addresses first (foreign key constraint)
            $stmt = $this->pdo->prepare("DELETE FROM addresses WHERE contact_id = ?");
            $stmt->execute([$id]);
            
            // Delete the contact
            $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            
            return [
                'success' => true, 
                'message' => 'Contact deleted successfully'
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP deleteContact error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error deleting contact: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all contacts for a user
     * 
     * @param mixed $params User ID
     * @return mixed List of contacts or error response
     */
    public function getAllContacts($params)
    {
        try {
            // Extract user ID from parameters
            $userId = null;
            
            if (is_array($params) && isset($params['user_id'])) {
                $userId = $params['user_id'];
            } elseif (is_object($params) && isset($params->user_id)) {
                $userId = $params->user_id;
            } else {
                $userId = $params;
            }
            
            // Convert to integer
            $userId = (int)$userId;
            
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Get all contacts for the user
            $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $contacts = $stmt->fetchAll();
            
            return [
                'success' => true,
                'contacts' => $contacts
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP getAllContacts error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving contacts: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create a new address for a contact
     * 
     * @param mixed $address Address data
     * @return array Response with success status and message
     */
    public function createAddress($address)
    {
        try {
            // Debug the input
            error_log('SOAP createAddress input: ' . print_r($address, true));
            
            // Extract address data from parameters
            $addressData = [];
            
            if (is_array($address) && isset($address['address'])) {
                $addressData = $address['address'];
            } elseif (is_object($address) && isset($address->address)) {
                $addressData = (array)$address->address;
            } elseif (is_array($address)) {
                $addressData = $address;
            } elseif (is_object($address)) {
                $addressData = (array)$address;
            }
            
            // Validate required fields
            if (empty($addressData['contact_id']) || empty($addressData['country'])) {
                return ['success' => false, 'message' => 'Contact ID and country are required'];
            }
            
            // Check if contact exists
            $stmt = $this->pdo->prepare("SELECT id FROM contacts WHERE id = ?");
            $stmt->execute([(int)$addressData['contact_id']]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            // Create the address with current timestamp
            $currentTime = date('Y-m-d H:i:s');
            $stmt = $this->pdo->prepare("INSERT INTO addresses (contact_id, street, city, province, country, postal_code, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                (int)$addressData['contact_id'],
                $addressData['street'] ?? null,
                $addressData['city'] ?? null,
                $addressData['province'] ?? null,
                $addressData['country'],
                $addressData['postal_code'] ?? null,
                $currentTime,
                $currentTime
            ]);
            
            $addressId = $this->pdo->lastInsertId();
            
            return [
                'success' => true, 
                'message' => 'Address created successfully', 
                'address_id' => $addressId
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP createAddress error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error creating address: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get address by ID
     * 
     * @param mixed $params Address ID
     * @return mixed Address data or error response
     */
    public function getAddress($params)
    {
        try {
            // Extract ID from parameters
            $id = null;
            
            if (is_array($params) && isset($params['id'])) {
                $id = $params['id'];
            } elseif (is_object($params) && isset($params->id)) {
                $id = $params->id;
            } else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            // Get address from database
            $stmt = $this->pdo->prepare("SELECT * FROM addresses WHERE id = ?");
            $stmt->execute([$id]);
            $address = $stmt->fetch();
            
            if (!$address) {
                return ['success' => false, 'message' => 'Address not found'];
            }
            
            return $address;
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP getAddress error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving address: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update an address
     * 
     * @param mixed $params Address data with ID
     * @return array Response with success status and message
     */
    public function updateAddress($params)
    {
        try {
            // Debug the input
            error_log('SOAP updateAddress input: ' . print_r($params, true));
            
            // Extract address data from parameters
            $addressData = [];
            
            if (is_array($params) && isset($params['address'])) {
                $addressData = $params['address'];
            } elseif (is_object($params) && isset($params->address)) {
                $addressData = (array)$params->address;
            } elseif (is_array($params)) {
                $addressData = $params;
            } elseif (is_object($params)) {
                $addressData = (array)$params;
            }
            
            // Validate required fields
            if (empty($addressData['id'])) {
                return ['success' => false, 'message' => 'Address ID is required'];
            }
            
            if (empty($addressData['contact_id'])) {
                return ['success' => false, 'message' => 'Contact ID is required'];
            }
            
            // Check if address exists
            $stmt = $this->pdo->prepare("SELECT a.id FROM addresses a JOIN contacts c ON a.contact_id = c.id WHERE a.id = ?");
            $stmt->execute([(int)$addressData['id']]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Address not found'];
            }
            
            // Check if address belongs to the specified contact
            $stmt = $this->pdo->prepare("SELECT id FROM addresses WHERE id = ? AND contact_id = ?");
            $stmt->execute([(int)$addressData['id'], (int)$addressData['contact_id']]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Address does not belong to the specified contact'];
            }
            
            // Build update query dynamically based on provided fields
            $updateFields = [];
            $params = [];
            
            if (isset($addressData['street'])) {
                $updateFields[] = "street = ?";
                $params[] = $addressData['street'];
            }
            
            if (isset($addressData['city'])) {
                $updateFields[] = "city = ?";
                $params[] = $addressData['city'];
            }
            
            if (isset($addressData['province'])) {
                $updateFields[] = "province = ?";
                $params[] = $addressData['province'];
            }
            
            if (isset($addressData['country'])) {
                $updateFields[] = "country = ?";
                $params[] = $addressData['country'];
            }
            
            if (isset($addressData['postal_code'])) {
                $updateFields[] = "postal_code = ?";
                $params[] = $addressData['postal_code'];
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No fields to update'];
            }
            
            // Add updated_at timestamp
            $updateFields[] = "updated_at = ?";
            $params[] = date('Y-m-d H:i:s');
            
            // Add address ID to params
            $params[] = (int)$addressData['id'];
            
            // Update the address
            $sql = "UPDATE addresses SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true, 
                'message' => 'Address updated successfully'
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP updateAddress error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error updating address: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete an address
     * 
     * @param mixed $params Address ID
     * @return array Response with success status and message
     */
    public function deleteAddress($params)
    {
        try {
            // Extract ID from parameters
            $id = null;
            
            if (is_array($params) && isset($params['id'])) {
                $id = $params['id'];
            } elseif (is_object($params) && isset($params->id)) {
                $id = $params->id;
            } else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            // Check if address exists
            $stmt = $this->pdo->prepare("SELECT id FROM addresses WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Address not found'];
            }
            
            // Delete the address
            $stmt = $this->pdo->prepare("DELETE FROM addresses WHERE id = ?");
            $stmt->execute([$id]);
            
            return [
                'success' => true, 
                'message' => 'Address deleted successfully'
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP deleteAddress error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error deleting address: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all addresses for a contact
     * 
     * @param mixed $params Contact ID
     * @return mixed List of addresses or error response
     */
    public function getContactAddresses($params)
    {
        try {
            // Extract contact ID from parameters
            $contactId = null;
            
            if (is_array($params) && isset($params['contact_id'])) {
                $contactId = $params['contact_id'];
            } elseif (is_object($params) && isset($params->contact_id)) {
                $contactId = $params->contact_id;
            } else {
                $contactId = $params;
            }
            
            // Convert to integer
            $contactId = (int)$contactId;
            
            // Check if contact exists
            $stmt = $this->pdo->prepare("SELECT id FROM contacts WHERE id = ?");
            $stmt->execute([$contactId]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            // Get all addresses for the contact
            $stmt = $this->pdo->prepare("SELECT * FROM addresses WHERE contact_id = ?");
            $stmt->execute([$contactId]);
            $addresses = $stmt->fetchAll();
            
            return [
                'success' => true,
                'addresses' => $addresses
            ];
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log('SOAP getContactAddresses error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving addresses: ' . $e->getMessage()];
        }
    }
}

// Instantiate service
$service = new ContactService($pdo);

// Check if WSDL is requested
if (isset($_GET['wsdl'])) {
    // Serve the WSDL file
    $wsdlPath = __DIR__ . '/api_documentation.wsdl';
    if (file_exists($wsdlPath)) {
        header('Content-Type: text/xml');
        readfile($wsdlPath);
    } else {
        header('HTTP/1.1 404 Not Found');
        echo 'WSDL file not found';
    }
    exit;
}

// Create SOAP server
$options = [
    'uri' => 'http://localhost/contact-api/soap/api'
];

// Check if WSDL mode is requested
if (isset($_GET['mode']) && $_GET['mode'] === 'wsdl') {
    $wsdlUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/contact-api/public/soap/server.php?wsdl';
    $server = new SoapServer($wsdlUrl, ['cache_wsdl' => WSDL_CACHE_NONE]);
} else {
    // Use non-WSDL mode (default)
    $server = new SoapServer(null, $options);
}

// Set the service object
$server->setObject($service);

// Handle the request
$server->handle();
