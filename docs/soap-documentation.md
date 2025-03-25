# SOAP API Documentation for Contact and Address Management

This document provides information on how to use the SOAP API services for managing Contacts and Addresses in the Contact API system.

## Overview

The SOAP API provides a set of operations to create, read, update, and delete Contacts and Addresses. The API is described using WSDL (Web Services Description Language) and follows the SOAP protocol for data exchange.

## WSDL Location

The WSDL file is available at:
```
http://localhost/contact-api/public/soap/contact.wsdl
```

## Available Operations

### Contact Operations

1. **createContact** - Create a new contact
   - Input: Contact object with user_id, first_name, last_name, email, phone
   - Output: Response object with success status and message

2. **getContact** - Get a contact by ID
   - Input: Contact ID
   - Output: Contact object

3. **updateContact** - Update an existing contact
   - Input: Contact object with id, first_name, last_name, email, phone
   - Output: Response object with success status and message

4. **deleteContact** - Delete a contact
   - Input: Contact ID
   - Output: Response object with success status and message

5. **getAllContacts** - Get all contacts for a user
   - Input: User ID
   - Output: Array of Contact objects

### Address Operations

1. **createAddress** - Create a new address
   - Input: Address object with contact_id, street, city, province, country, postal_code
   - Output: Response object with success status and message

2. **getAddress** - Get an address by ID
   - Input: Address ID
   - Output: Address object

3. **updateAddress** - Update an existing address
   - Input: Address object with id, contact_id, street, city, province, country, postal_code
   - Output: Response object with success status and message

4. **deleteAddress** - Delete an address
   - Input: Address ID
   - Output: Response object with success status and message

5. **getContactAddresses** - Get all addresses for a contact
   - Input: Contact ID
   - Output: Array of Address objects

## Data Types

### Contact
- **id** (int) - Contact ID
- **user_id** (int) - User ID
- **first_name** (string) - First name
- **last_name** (string) - Last name
- **email** (string) - Email address
- **phone** (string) - Phone number
- **created_at** (string) - Creation timestamp
- **updated_at** (string) - Last update timestamp

### Address
- **id** (int) - Address ID
- **contact_id** (int) - Contact ID
- **street** (string) - Street address
- **city** (string) - City
- **province** (string) - Province/State
- **country** (string) - Country
- **postal_code** (string) - Postal/ZIP code
- **created_at** (string) - Creation timestamp
- **updated_at** (string) - Last update timestamp

### Response
- **success** (boolean) - Operation success status
- **message** (string) - Response message

## Example Usage (PHP)

### Creating a SOAP Client

```php
$options = [
    'cache_wsdl' => 0, // WSDL_CACHE_NONE
    'trace' => 1,
    'exceptions' => true
];
$client = new SoapClient('http://localhost/contact-api/public/soap/contact.wsdl', $options);
```

### Creating a Contact

```php
$contact = [
    'user_id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '123-456-7890'
];
$result = $client->createContact($contact);
```

### Getting a Contact

```php
$result = $client->getContact(1); // Get contact with ID 1
```

### Creating an Address

```php
$address = [
    'contact_id' => 1,
    'street' => '123 Main St',
    'city' => 'Anytown',
    'province' => 'State',
    'country' => 'Country',
    'postal_code' => '12345'
];
$result = $client->createAddress($address);
```

## Testing the SOAP API

A simple SOAP client interface is available at:
```
http://localhost/contact-api/public/soap/client.php
```

This interface allows you to test all available SOAP operations through a user-friendly web interface.

## Integration with REST API

The SOAP API uses the same database and models as the REST API, ensuring data consistency across both interfaces. You can use either API based on your application requirements.

## Error Handling

All operations return appropriate error messages in case of failures. Common error scenarios include:
- Invalid or missing required parameters
- Entity not found (when trying to update or delete)
- Database errors

## Security Considerations

- The SOAP API does not currently implement authentication. In a production environment, consider adding authentication mechanisms.
- Always validate and sanitize input data before passing it to the SOAP API.
