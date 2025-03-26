<?php
// Check if SOAP extension is loaded
if (extension_loaded('soap')) {
    echo '<div style="background-color: #111827; color: #10b981; padding: 20px; font-family: Arial, sans-serif; border-radius: 8px; max-width: 600px; margin: 50px auto; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
    echo '<h1 style="background: linear-gradient(to right, #4F46E5, #EC4899); -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center;">Extension SOAP Berhasil Diaktifkan</h1>';
    echo '</div>';
} else {
    echo '<div style="background-color: #111827; color: #ef4444; padding: 20px; font-family: Arial, sans-serif; border-radius: 8px; max-width: 600px; margin: 50px auto; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">';
    echo '<h1 style="background: linear-gradient(to right, #DC2626, #7F1D1D); -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center;">Extension SOAP Belum Diaktifkan</h1>';
    echo '</div>';
}
?>
