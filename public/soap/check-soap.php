<?php
// Check if SOAP extension is loaded
if (extension_loaded('soap')) {
    echo '<div style="background-color: #111827; color: #10b981; padding: 20px; font-family: Arial, sans-serif; border-radius: 8px; max-width: 600px; margin: 50px auto; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
    echo '<h1 style="background: linear-gradient(to right, #4F46E5, #EC4899); -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center;">SOAP Extension is Enabled!</h1>';
    echo '<p>You can now use the original SOAP implementation.</p>';
    echo '<h2 style="color: #e2e8f0;">Next Steps:</h2>';
    echo '<ol>';
    echo '<li>Access the SOAP client at: <a href="../soap/client.php" style="color: #4F46E5;">http://localhost/contact-api/public/soap/client.php</a></li>';
    echo '<li>The SOAP server is available at: <a href="../soap/server.php" style="color: #4F46E5;">http://localhost/contact-api/public/soap/server.php</a></li>';
    echo '<li>The WSDL file is at: <a href="../soap/contact.wsdl" style="color: #4F46E5;">http://localhost/contact-api/public/soap/contact.wsdl</a></li>';
    echo '</ol>';
    echo '</div>';
} else {
    echo '<div style="background-color: #111827; color: #ef4444; padding: 20px; font-family: Arial, sans-serif; border-radius: 8px; max-width: 600px; margin: 50px auto; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
    echo '<h1 style="background: linear-gradient(to right, #DC2626, #7F1D1D); -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center;">SOAP Extension is NOT Enabled</h1>';
    echo '<p>Please follow these steps to enable the SOAP extension:</p>';
    echo '<ol>';
    echo '<li>Open your php.ini file (located at C:\xampp\php\php.ini)</li>';
    echo '<li>Find the line <code>;extension=soap</code> and remove the semicolon at the beginning to make it <code>extension=soap</code></li>';
    echo '<li>Save the file</li>';
    echo '<li>Restart the Apache server in XAMPP Control Panel</li>';
    echo '<li>Refresh this page</li>';
    echo '</ol>';
    echo '<p>If you still see this message after following these steps, you may need to check your XAMPP installation or try using the alternative implementation at: <a href="../soap/client-alternative.php" style="color: #4F46E5;">http://localhost/contact-api/public/soap/client-alternative.php</a></p>';
    echo '</div>';
}
?>
