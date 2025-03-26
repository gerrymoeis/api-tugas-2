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
            // Handle different parameter formats (array or object)
            $userData = [];
            
            // Debug the input
            error_log('SOAP createUser input: ' . print_r($user, true));
            
            // For non-WSDL mode, the parameter comes as the first element of an array
            if (is_array($user) && isset($user[0])) {
                $user = $user[0];
            }
            
            if (is_array($user)) {
                // Direct array parameter
                $userData = $user;
            } elseif (is_object($user)) {
                // Object parameter
                $userData['name'] = $user->name ?? null;
                $userData['email'] = $user->email ?? null;
            } else {
                // Unexpected format
                return ['success' => false, 'message' => 'Invalid user data format'];
            }
            
            // Validate required fields
            if (empty($userData['name']) || empty($userData['email'])) {
                return ['success' => false, 'message' => 'Name and email are required'];
            }
            
            // Generate a username from email
            $username = explode('@', $userData['email'])[0];
            
            // Create the user
            $newUser = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'username' => $username,
                'password' => bcrypt('password123') // Default password
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
     * @param mixed $id User ID
     * @return stdClass|array User data or error response
     */
    public function getUser($id)
    {
        try {
            // Debug the input
            error_log('SOAP getUser input: ' . print_r($id, true));
            
            // For non-WSDL mode, the parameter might come as an array
            if (is_array($id) && isset($id[0])) {
                $id = $id[0];
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
            
            // For non-WSDL mode, the parameter comes as the first element of an array
            if (is_array($contact) && isset($contact[0])) {
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
                'phone' => $contactData['phone'] ?? null
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
     * @param int $id Contact ID
     * @return stdClass|array Contact data or error response
     */
    public function getContact($id)
    {
        try {
            $contact = Contact::find($id);
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }

            return $contact;
        } catch (Exception $e) {
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
            
            // For non-WSDL mode, the parameter comes as the first element of an array
            if (is_array($contact) && isset($contact[0])) {
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
     * @param mixed $id Contact ID
     * @return array Response with success status and message
     */
    public function deleteContact($id)
    {
        try {
            // Debug the input
            error_log('SOAP deleteContact input: ' . print_r($id, true));
            
            // For non-WSDL mode, the parameter might come as an array
            if (is_array($id) && isset($id[0])) {
                $id = $id[0];
            }
            
            // Convert to integer
            $id = (int)$id;
            
            $contact = Contact::find($id);
            if (!$contact) {
                return ['success' => false, 'message' => 'Contact not found'];
            }

            // Delete related addresses first
            Address::where('contact_id', $id)->delete();
            
            // Delete the contact
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
     * @param int $user_id User ID
     * @return array Array of contacts
     */
    public function getAllContacts($user_id)
    {
        try {
            $contacts = Contact::where('user_id', $user_id)->get()->toArray();
            return ['contact' => $contacts];
        } catch (Exception $e) {
            return ['contact' => []];
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
            
            // For non-WSDL mode, the parameter comes as the first element of an array
            if (is_array($address) && isset($address[0])) {
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
     * @param int $id Address ID
     * @return stdClass|array Address data or error response
     */
    public function getAddress($id)
    {
        try {
            $address = Address::find($id);
            if (!$address) {
                return ['success' => false, 'message' => 'Address not found'];
            }

            return $address;
        } catch (Exception $e) {
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
            
            // For non-WSDL mode, the parameter comes as the first element of an array
            if (is_array($address) && isset($address[0])) {
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
     * @param mixed $id Address ID
     * @return array Response with success status and message
     */
    public function deleteAddress($id)
    {
        try {
            // Debug the input
            error_log('SOAP deleteAddress input: ' . print_r($id, true));
            
            // For non-WSDL mode, the parameter might come as an array
            if (is_array($id) && isset($id[0])) {
                $id = $id[0];
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
     * @param int $contact_id Contact ID
     * @return array Array of addresses
     */
    public function getContactAddresses($contact_id)
    {
        try {
            $addresses = Address::where('contact_id', $contact_id)->get()->toArray();
            return ['address' => $addresses];
        } catch (Exception $e) {
            return ['address' => []];
        }
    }
}

// Initialize SOAP Server
try {
    // Check if SOAP extension is loaded
    if (!extension_loaded('soap')) {
        header('Content-Type: text/plain');
        echo 'Error: SOAP extension is not loaded. Please enable the SOAP extension in your PHP configuration.';
        exit;
    }
    
    if (isset($_GET['wsdl'])) {
        // Return the WSDL
        header('Content-Type: text/xml');
        readfile(__DIR__ . '/contact.wsdl');
    } else {
        // Handle SOAP request
        $server = new SoapServer(null, [
            'uri' => 'http://localhost/contact-api/soap/contact',
            'encoding' => 'UTF-8'
        ]);
        $server->setClass('ContactService');
        $server->handle();
    }
} catch (Exception $e) {
    header('Content-Type: text/plain');
    echo 'SOAP Server Error: ' . $e->getMessage();
}
