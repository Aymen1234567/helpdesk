<?php
echo "Hash des mots de passe corrects :\n";
echo "tuteur123 : " . password_hash('tuteur123', PASSWORD_DEFAULT) . "\n";
echo "etudiant123 : " . password_hash('etudiant123', PASSWORD_DEFAULT) . "\n";
?>
