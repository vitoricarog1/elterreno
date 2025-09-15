<?php
// Verificar se a extensão GD está habilitada
if (extension_loaded('gd')) {
    echo "<h2 style='color: green;'>✅ Extensão GD está habilitada!</h2>";
    $info = gd_info();
    echo "<pre>";
    print_r($info);
    echo "</pre>";
} else {
    echo "<h2 style='color: red;'>❌ Extensão GD NÃO está habilitada!</h2>";
    echo "<h3>Para habilitar a extensão GD:</h3>";
    echo "<ol>";
    echo "<li>Abra o arquivo <strong>C:\\xampp\\php\\php.ini</strong></li>";
    echo "<li>Procure pela linha <code>;extension=gd</code> (linha 931)</li>";
    echo "<li>Remova o ponto e vírgula (;) do início da linha</li>";
    echo "<li>A linha deve ficar: <code>extension=gd</code></li>";
    echo "<li>Salve o arquivo</li>";
    echo "<li>Reinicie o Apache no painel do XAMPP</li>";
    echo "</ol>";
    echo "<p><strong>Depois de fazer essas alterações, recarregue esta página para verificar.</strong></p>";
}
?>