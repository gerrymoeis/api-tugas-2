<?php

// Bootstrap Laravel application
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Contact;
use App\Models\Address;
use App\Models\User;

/**
 * ContactService class - Implements SOAP operations for Contact and Address
 */
class ContactService
{
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
            
            // For WSDL mode, extract user from parameters
            if (isset($user->user)) {
                $user = $user->user;
            }
            // For non-WSDL mode, the parameter comes as the first element of an array
            elseif (is_array($user) && isset($user[0])) {
                $user = $user[0];
            }
            
            // Handle different parameter formats
            $userData = [];
            
            if (is_array($user)) {
                // Direct array parameter
                $userData = $user;
            } elseif (is_object($user)) {
                // Object parameter
                $userData['name'] = $user->name ?? null;
                $userData['email'] = $user->email ?? null;
                $userData['username'] = $user->username ?? null;
            } else {
                // Unexpected format
                return ['success' => false, 'message' => 'Invalid user data format'];
            }
            
            // Validate required fields
            if (empty($userData['name']) || empty($userData['email'])) {
                return ['success' => false, 'message' => 'Name and email are required'];
            }
            
            // Check if user with this email already exists
            $existingUser = User::where('email', $userData['email'])->first();
            if ($existingUser) {
                return ['success' => false, 'message' => 'User with this email already exists'];
            }
            
            // Create the user
            $newUser = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'username' => $userData['username'] ?? '',
                'password' => bcrypt('password') // Default password
            ]);
            
            return [
                'success' => true, 
                'message' => 'User created successfully', 
                'user_id' => $newUser->id
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP createUser error: ' . $e->getMessage() . ' | User data: ' . print_r($user, true));
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
            // Debug the input
            error_log('SOAP getUser input: ' . print_r($params, true));
            
            // Extract ID from different parameter formats
            $id = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->id)) {
                $id = $params->id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $id = is_object($params[0]) && isset($params[0]->id) ? $params[0]->id : $params[0];
            }
            // Direct parameter
            else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            $user = User::find($id);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username
            ];
        } catch (Exception $e) {
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
            
            // For WSDL mode, extract contact from parameters
            if (isset($contact->contact)) {
                $contact = $contact->contact;
            }
            // For non-WSDL mode, the parameter comes as the first element of an array
            elseif (is_array($contact) && isset($contact[0])) {
                $contact = $contact[0];
            }
            
            // Handle different parameter formats
            $contactData = [];
            
            if (is_array($contact)) {
                // Direct array parameter
                $contactData = $contact;
            } elseif (is_object($contact)) {
                // Object parameter
                $contactData['user_id'] = $contact->user_id ?? null;
                $contactData['first_name'] = $contact->first_name ?? null;
                $contactData['last_name'] = $contact->last_name ?? null;
                $contactData['email'] = $contact->email ?? null;
                $contactData['phone'] = $contact->phone ?? null;
            } else {
                // Unexpected format
                return ['success' => false, 'message' => 'Invalid contact data format'];
            }
            
            // Validate required fields
            if (empty($contactData['user_id']) || empty($contactData['first_name']) || empty($contactData['email'])) {
                return ['success' => false, 'message' => 'User ID, first name, and email are required'];
            }
            
            // Check if user exists
            $user = User::find($contactData['user_id']);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Create the contact
            $newContact = Contact::create([
                'user_id' => $contactData['user_id'],
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'] ?? '',
                'email' => $contactData['email'],
                'phone' => $contactData['phone'] ?? ''
            ]);
            
            return [
                'success' => true, 
                'message' => 'Contact created successfully', 
                'contact_id' => $newContact->id
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP createContact error: ' . $e->getMessage() . ' | Contact data: ' . print_r($contact, true));
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
            // Debug the input
            error_log('SOAP getContact input: ' . print_r($params, true));
            
            // Extract ID from different parameter formats
            $id = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->id)) {
                $id = $params->id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $id = is_object($params[0]) && isset($params[0]->id) ? $params[0]->id : $params[0];
            }
            // Direct parameter
            else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            $contact = Contact::find($id);
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            return [
                'id' => $contact->id,
                'user_id' => $contact->user_id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'email' => $contact->email,
                'phone' => $contact->phone
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP getContact error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving contact: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update an existing contact
     * 
     * @param mixed $contact Contact data with ID
     * @return array Response with success status and message
     */
    public function updateContact($contact)
    {
        try {
            // Debug the input
            error_log('SOAP updateContact input: ' . print_r($contact, true));
            
            // For WSDL mode, extract contact from parameters
            if (isset($contact->contact)) {
                $contact = $contact->contact;
            }
            // For non-WSDL mode, the parameter comes as the first element of an array
            elseif (is_array($contact) && isset($contact[0])) {
                $contact = $contact[0];
            }
            
            // Handle different parameter formats
            $contactData = [];
            
            if (is_array($contact)) {
                // Direct array parameter
                $contactData = $contact;
            } elseif (is_object($contact)) {
                // Object parameter
                $contactData['id'] = $contact->id ?? null;
                $contactData['first_name'] = $contact->first_name ?? null;
                $contactData['last_name'] = $contact->last_name ?? null;
                $contactData['email'] = $contact->email ?? null;
                $contactData['phone'] = $contact->phone ?? null;
            } else {
                // Unexpected format
                return ['success' => false, 'message' => 'Invalid contact data format'];
            }
            
            // Validate required fields
            if (empty($contactData['id']) || empty($contactData['first_name']) || empty($contactData['email'])) {
                return ['success' => false, 'message' => 'Contact ID, first name, and email are required'];
            }
            
            $existingContact = Contact::find($contactData['id']);
            if (!$existingContact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }

            $existingContact->first_name = $contactData['first_name'];
            $existingContact->last_name = $contactData['last_name'] ?? $existingContact->last_name;
            $existingContact->email = $contactData['email'];
            $existingContact->phone = $contactData['phone'] ?? $existingContact->phone;
            $existingContact->save();

            return ['success' => true, 'message' => 'Contact updated successfully'];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP updateContact error: ' . $e->getMessage() . ' | Contact data: ' . print_r($contact, true));
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
            // Debug the input
            error_log('SOAP deleteContact input: ' . print_r($params, true));
            
            // Extract ID from different parameter formats
            $id = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->id)) {
                $id = $params->id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $id = is_object($params[0]) && isset($params[0]->id) ? $params[0]->id : $params[0];
            }
            // Direct parameter
            else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            $contact = Contact::find($id);
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }

            $contact->delete();

            return ['success' => true, 'message' => 'Contact deleted successfully'];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP deleteContact error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error deleting contact: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all contacts for a user
     * 
     * @param mixed $params User ID
     * @return array Contacts array or error response
     */
    public function getAllContacts($params)
    {
        try {
            // Debug the input
            error_log('SOAP getAllContacts input: ' . print_r($params, true));
            
            // Extract user_id from different parameter formats
            $userId = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->user_id)) {
                $userId = $params->user_id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $userId = is_object($params[0]) && isset($params[0]->user_id) ? $params[0]->user_id : $params[0];
            }
            // Direct parameter
            else {
                $userId = $params;
            }
            
            // Convert to integer
            $userId = (int)$userId;
            
            // Check if user exists
            $user = User::find($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Get all contacts for this user
            $contacts = Contact::where('user_id', $userId)->get();
            
            $result = [];
            foreach ($contacts as $contact) {
                $result[] = [
                    'id' => $contact->id,
                    'user_id' => $contact->user_id,
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'email' => $contact->email,
                    'phone' => $contact->phone
                ];
            }
            
            return ['contact' => $result];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP getAllContacts error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving contacts: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create a new address
     * 
     * @param mixed $address Address data
     * @return array Response with success status and message
     */
    public function createAddress($address)
    {
        try {
            // Debug the input
            error_log('SOAP createAddress input: ' . print_r($address, true));
            
            // For WSDL mode, extract address from parameters
            if (isset($address->address)) {
                $address = $address->address;
            }
            // For non-WSDL mode, the parameter comes as the first element of an array
            elseif (is_array($address) && isset($address[0])) {
                $address = $address[0];
            }
            
            // Handle different parameter formats
            $addressData = [];
            
            if (is_array($address)) {
                // Direct array parameter
                $addressData = $address;
            } elseif (is_object($address)) {
                // Object parameter
                $addressData['contact_id'] = $address->contact_id ?? null;
                $addressData['street'] = $address->street ?? null;
                $addressData['city'] = $address->city ?? null;
                $addressData['province'] = $address->province ?? null;
                $addressData['country'] = $address->country ?? null;
                $addressData['postal_code'] = $address->postal_code ?? null;
            } else {
                // Unexpected format
                return ['success' => false, 'message' => 'Invalid address data format'];
            }
            
            // Validate required fields
            if (empty($addressData['contact_id']) || empty($addressData['street']) || empty($addressData['city'])) {
                return ['success' => false, 'message' => 'Contact ID, street, and city are required'];
            }
            
            // Check if contact exists
            $contact = Contact::find($addressData['contact_id']);
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            // Create the address
            $newAddress = Address::create([
                'contact_id' => $addressData['contact_id'],
                'street' => $addressData['street'],
                'city' => $addressData['city'],
                'province' => $addressData['province'] ?? '',
                'country' => $addressData['country'] ?? '',
                'postal_code' => $addressData['postal_code'] ?? ''
            ]);
            
            return [
                'success' => true, 
                'message' => 'Address created successfully', 
                'address_id' => $newAddress->id
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP createAddress error: ' . $e->getMessage() . ' | Address data: ' . print_r($address, true));
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
            // Debug the input
            error_log('SOAP getAddress input: ' . print_r($params, true));
            
            // Extract ID from different parameter formats
            $id = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->id)) {
                $id = $params->id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $id = is_object($params[0]) && isset($params[0]->id) ? $params[0]->id : $params[0];
            }
            // Direct parameter
            else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            $address = Address::find($id);
            if (!$address) {
                return ['success' => false, 'message' => 'Address not found'];
            }
            
            return [
                'id' => $address->id,
                'contact_id' => $address->contact_id,
                'street' => $address->street,
                'city' => $address->city,
                'province' => $address->province,
                'country' => $address->country,
                'postal_code' => $address->postal_code
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP getAddress error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving address: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update an existing address
     * 
     * @param mixed $address Address data with ID
     * @return array Response with success status and message
     */
    public function updateAddress($address)
    {
        try {
            // Debug the input
            error_log('SOAP updateAddress input: ' . print_r($address, true));
            
            // For WSDL mode, extract address from parameters
            if (isset($address->address)) {
                $address = $address->address;
            }
            // For non-WSDL mode, the parameter comes as the first element of an array
            elseif (is_array($address) && isset($address[0])) {
                $address = $address[0];
            }
            
            // Handle different parameter formats
            $addressData = [];
            
            if (is_array($address)) {
                // Direct array parameter
                $addressData = $address;
            } elseif (is_object($address)) {
                // Object parameter
                $addressData['id'] = $address->id ?? null;
                $addressData['street'] = $address->street ?? null;
                $addressData['city'] = $address->city ?? null;
                $addressData['province'] = $address->province ?? null;
                $addressData['country'] = $address->country ?? null;
                $addressData['postal_code'] = $address->postal_code ?? null;
            } else {
                // Unexpected format
                return ['success' => false, 'message' => 'Invalid address data format'];
            }
            
            // Validate required fields
            if (empty($addressData['id']) || empty($addressData['street']) || empty($addressData['city'])) {
                return ['success' => false, 'message' => 'Address ID, street, and city are required'];
            }

            $existingAddress = Address::find($addressData['id']);
            if (!$existingAddress) {
                return ['success' => false, 'message' => 'Address not found'];
            }

            $existingAddress->street = $addressData['street'];
            $existingAddress->city = $addressData['city'];
            $existingAddress->province = $addressData['province'] ?? $existingAddress->province;
            $existingAddress->country = $addressData['country'] ?? $existingAddress->country;
            $existingAddress->postal_code = $addressData['postal_code'] ?? $existingAddress->postal_code;
            $existingAddress->save();

            return ['success' => true, 'message' => 'Address updated successfully'];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP updateAddress error: ' . $e->getMessage() . ' | Address data: ' . print_r($address, true));
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
            // Debug the input
            error_log('SOAP deleteAddress input: ' . print_r($params, true));
            
            // Extract ID from different parameter formats
            $id = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->id)) {
                $id = $params->id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $id = is_object($params[0]) && isset($params[0]->id) ? $params[0]->id : $params[0];
            }
            // Direct parameter
            else {
                $id = $params;
            }
            
            // Convert to integer
            $id = (int)$id;
            
            $address = Address::find($id);
            if (!$address) {
                return ['success' => false, 'message' => 'Address not found'];
            }

            $address->delete();

            return ['success' => true, 'message' => 'Address deleted successfully'];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP deleteAddress error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error deleting address: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all addresses for a contact
     * 
     * @param mixed $params Contact ID
     * @return array Addresses array or error response
     */
    public function getContactAddresses($params)
    {
        try {
            // Debug the input
            error_log('SOAP getContactAddresses input: ' . print_r($params, true));
            
            // Extract contact_id from different parameter formats
            $contactId = null;
            
            // For WSDL mode
            if (is_object($params) && isset($params->contact_id)) {
                $contactId = $params->contact_id;
            }
            // For non-WSDL mode, the parameter might come as an array
            elseif (is_array($params) && isset($params[0])) {
                $contactId = is_object($params[0]) && isset($params[0]->contact_id) ? $params[0]->contact_id : $params[0];
            }
            // Direct parameter
            else {
                $contactId = $params;
            }
            
            // Convert to integer
            $contactId = (int)$contactId;
            
            // Check if contact exists
            $contact = Contact::find($contactId);
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }
            
            // Get all addresses for this contact
            $addresses = Address::where('contact_id', $contactId)->get();
            
            $result = [];
            foreach ($addresses as $address) {
                $result[] = [
                    'id' => $address->id,
                    'contact_id' => $address->contact_id,
                    'street' => $address->street,
                    'city' => $address->city,
                    'province' => $address->province,
                    'country' => $address->country,
                    'postal_code' => $address->postal_code
                ];
            }
            
            return ['address' => $result];
        } catch (Exception $e) {
            // Log the error for debugging
            error_log('SOAP getContactAddresses error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error retrieving addresses: ' . $e->getMessage()];
        }
    }
}

// Check if WSDL is requested
if (isset($_GET['wsdl'])) {
    // Serve the WSDL file
    $wsdlPath = __DIR__ . '/contact.wsdl';
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
    'uri' => 'http://localhost/contact-api/soap/contact'
];

// Check if WSDL mode is requested
if (isset($_GET['mode']) && $_GET['mode'] === 'wsdl') {
    $wsdlUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/contact-api/public/soap/server.php?wsdl';
    $server = new SoapServer($wsdlUrl, ['cache_wsdl' => WSDL_CACHE_NONE]);
} else {
    // Use non-WSDL mode (default)
    $server = new SoapServer(null, $options);
}

// Set the class which contains the SOAP functions
$server->setClass('ContactService');

// Handle the request
$server->handle();
