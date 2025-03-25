<?php
// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact API - SOAP Alternative Client</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #111827;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            background-image: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }
        .container::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            right: -50px;
            bottom: -50px;
            background: radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.1) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
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
            text-shadow: 0 0 10px rgba(236, 72, 153, 0.3);
        }
        .card {
            background-color: #1f2937;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(79, 70, 229, 0.2);
        }
        pre {
            background-color: #374151;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            color: #10b981;
            border-left: 3px solid #4F46E5;
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
            transition: all 0.3s ease;
        }
        button:hover {
            opacity: 0.9;
            box-shadow: 0 0 15px rgba(236, 72, 153, 0.5);
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
            transition: all 0.3s ease;
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
        /* Particle effect */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background-color: rgba(236, 72, 153, 0.3);
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>
    <div class="container">
        <h1>Contact API - SOAP Alternative Client</h1>
        
        <div class="tabs">
            <div class="tab active" data-tab="contact-operations">Contact Operations</div>
            <div class="tab" data-tab="address-operations">Address Operations</div>
        </div>
        
        <!-- Contact Operations Tab -->
        <div class="tab-content active" id="contact-operations">
            <!-- Create Contact Form -->
            <div class="card">
                <h2>Create Contact</h2>
                <form method="post" action="soap-alternative.php">
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
            </div>
            
            <!-- Get Contact Form -->
            <div class="card">
                <h2>Get Contact</h2>
                <form method="post" action="soap-alternative.php">
                    <input type="hidden" name="action" value="getContact">
                    <div class="form-group">
                        <label for="contact_id">Contact ID:</label>
                        <input type="number" id="contact_id" name="contact_id" required>
                    </div>
                    <button type="submit">Get Contact</button>
                </form>
            </div>
            
            <!-- Update Contact Form -->
            <div class="card">
                <h2>Update Contact</h2>
                <form method="post" action="soap-alternative.php">
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
            </div>
            
            <!-- Delete Contact Form -->
            <div class="card">
                <h2>Delete Contact</h2>
                <form method="post" action="soap-alternative.php">
                    <input type="hidden" name="action" value="deleteContact">
                    <div class="form-group">
                        <label for="delete_contact_id">Contact ID:</label>
                        <input type="number" id="delete_contact_id" name="contact_id" required>
                    </div>
                    <button type="submit">Delete Contact</button>
                </form>
            </div>
            
            <!-- Get All Contacts Form -->
            <div class="card">
                <h2>Get All Contacts</h2>
                <form method="post" action="soap-alternative.php">
                    <input type="hidden" name="action" value="getAllContacts">
                    <div class="form-group">
                        <label for="all_contacts_user_id">User ID:</label>
                        <input type="number" id="all_contacts_user_id" name="user_id" required>
                    </div>
                    <button type="submit">Get All Contacts</button>
                </form>
            </div>
        </div>
        
        <!-- Address Operations Tab -->
        <div class="tab-content" id="address-operations">
            <!-- Create Address Form -->
            <div class="card">
                <h2>Create Address</h2>
                <form method="post" action="soap-alternative.php">
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
            </div>
            
            <!-- Get Address Form -->
            <div class="card">
                <h2>Get Address</h2>
                <form method="post" action="soap-alternative.php">
                    <input type="hidden" name="action" value="getAddress">
                    <div class="form-group">
                        <label for="get_address_id">Address ID:</label>
                        <input type="number" id="get_address_id" name="address_id" required>
                    </div>
                    <button type="submit">Get Address</button>
                </form>
            </div>
            
            <!-- Update Address Form -->
            <div class="card">
                <h2>Update Address</h2>
                <form method="post" action="soap-alternative.php">
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
            </div>
            
            <!-- Delete Address Form -->
            <div class="card">
                <h2>Delete Address</h2>
                <form method="post" action="soap-alternative.php">
                    <input type="hidden" name="action" value="deleteAddress">
                    <div class="form-group">
                        <label for="delete_address_id">Address ID:</label>
                        <input type="number" id="delete_address_id" name="address_id" required>
                    </div>
                    <button type="submit">Delete Address</button>
                </form>
            </div>
            
            <!-- Get Contact Addresses Form -->
            <div class="card">
                <h2>Get Contact Addresses</h2>
                <form method="post" action="soap-alternative.php">
                    <input type="hidden" name="action" value="getContactAddresses">
                    <div class="form-group">
                        <label for="get_addresses_contact_id">Contact ID:</label>
                        <input type="number" id="get_addresses_contact_id" name="contact_id" required>
                    </div>
                    <button type="submit">Get Contact Addresses</button>
                </form>
            </div>
        </div>
        
        <!-- Display Response if any -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="card">
                <h2>API Response</h2>
                <pre><?php echo htmlspecialchars(file_get_contents('php://input')); ?></pre>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Tab functionality
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
            
            // Create particles
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random position
                const x = Math.random() * 100;
                const y = Math.random() * 100;
                
                particle.style.left = `${x}%`;
                particle.style.top = `${y}%`;
                particle.style.opacity = Math.random() * 0.5 + 0.1;
                
                // Random size
                const size = Math.random() * 3 + 1;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random color (blue to pink gradient)
                const hue = Math.random() * 60 + 240; // 240-300 range (blue to pink)
                particle.style.backgroundColor = `hsla(${hue}, 70%, 60%, 0.3)`;
                
                particlesContainer.appendChild(particle);
                
                // Animate particle
                animateParticle(particle);
            }
            
            function animateParticle(particle) {
                const duration = Math.random() * 15000 + 10000; // 10-25 seconds
                const xMove = (Math.random() - 0.5) * 20;
                const yMove = (Math.random() - 0.5) * 20;
                
                particle.animate([
                    { transform: 'translate(0, 0)', opacity: Math.random() * 0.5 + 0.1 },
                    { transform: `translate(${xMove}px, ${yMove}px)`, opacity: Math.random() * 0.3 + 0.05 }
                ], {
                    duration: duration,
                    iterations: Infinity,
                    direction: 'alternate',
                    easing: 'ease-in-out'
                });
            }
        });
    </script>
</body>
</html>
