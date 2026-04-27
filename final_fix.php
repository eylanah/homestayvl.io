<?php
$file = 'c:/xampp/htdocs/khachsan/bank_qr.php';
$lines = file($file);

// Find the line with "        </form>" followed by "      </div>" followed by "  </div>"
for ($i = 0; $i < count($lines); $i++) {
    if (trim($lines[$i]) === '</form>') {
        // Check next lines
        if (isset($lines[$i+1]) && trim($lines[$i+1]) === '</div>' && 
            isset($lines[$i+2]) && trim($lines[$i+2]) === '</div>') {
            // Insert the missing qr-card closing div between them
            // lines[$i+1] is "      </div>" (closes .mt-4)
            // We need to insert "    </div>" after it (closes .qr-card)
            // And change the next line to "  </div>" (closes .container)
            $lines[$i+1] = "      </div>\n";
            array_splice($lines, $i+2, 0, ["    </div>\n"]);
            // The original line at $i+2 was "  </div>" which we'll keep as is
            // Now it becomes $i+3
            if (isset($lines[$i+3])) {
                $lines[$i+3] = "  </div>\n";
            }
            break;
        }
    }
}

file_put_contents($file, implode('', $lines));
echo 'Fixed final!';
?>

