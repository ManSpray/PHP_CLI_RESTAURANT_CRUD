#!php
<?php

require_once('src/models/Visitor.php');
if ($argc == 1 || $argv[1] == '--help') {
?>

    This is a command line application used to register and manage details about restaurant visitors during COVID-19 breakdown.

    Choose action:

    [1] - Add new visitor
    [2] - Edit visitor
    [3] - Delete visitor
    [4] - Show all visitors
    [0] - Exit

<?php
    $i = readline("Action number: ");
    $fileName = 'data/visitorsJournal.csv';

    function readFromFile()
    {
        echo "ID" . chr(9) . chr(9) . "Name" . chr(9) . chr(9) . "Surname" . chr(9) . chr(9) . "Email" . chr(9) . chr(9) . chr(9) . chr(9) . "Phone" . chr(9) . chr(9) . chr(9) . "Time date\n";
        if (($handle = fopen($GLOBALS['fileName'], "r")) !== FALSE) {
            while (($array = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($array);
                for ($c = 0; $c < $num; $c++) {
                    echo $array[$c] . chr(9) . chr(9);
                }
                echo "\n";
            }
            fclose($handle);
        }
    }

    function newVisitor($visitor)
    {
        $visitor->id = readline("ID:");
        $visitor->name = readline("Name:");
        $visitor->surname = readline("Surname:");

        // email validation
        while (true) {
            $visitor->email = readline("Email:");
            $email_status = filter_var($visitor->email, FILTER_VALIDATE_EMAIL);
            if (!$email_status) {
                echo "Invalid EMAIL, please enter again\n";
            }else break;
        }

        //phone validation
        while (true) {
            $visitor->phone = readline("Phone:");
            if (preg_match('/^[0-9]{9}+$/', $visitor->phone) != true){
                echo "Invalid PHONE NUMBER, it must contain 9 digits, please enter again\n";
            }else break;
            
        }
        $visitor->dateTime = readline("Date Time:");
    }
    // CRUD operations switch
    switch ($i) {
        case 0:
            echo "Stay safe!\n";
            break;

            //Add new visitor
        case 1:
            echo "Please enter the following details:\n";
            $visitor = new Visitor;
            newVisitor($visitor);
            $array = (array) $visitor;
            $fp = fopen($GLOBALS['fileName'], 'a');
            fputcsv($fp, $array);
            fclose($fp);
            break;

            //Edit visitor
        case 2:
            readFromFile();
            $toEdit = readline("\nPlease enter visitor ID which you want to edit:");
            $contents = file($fileName);
            $i = count($contents);
            while ($i >= 0) {
                if ($contents[$i][0] == $toEdit) {
                    $key = $i;
                    $editedContent = new Visitor;
                    newVisitor($editedContent);
                    $editable = (array) $editedContent;
                    $edited = implode($glue = ",", $editable);
                    $edited .= "\n";
                    $contents[$key] = $edited;
                    break;
                } else if ($i == 0) {
                    print_r('Error! ID not found.');
                }
                $i--;
            }
            $fp = fopen($fileName, 'w');
            file_put_contents($fileName, $contents);
            fclose($fp);
            break;

            //Delete visitor from list
        case 3:
            readFromFile();
            $toDelete = readline("\nPlease enter visitor ID which you want to delete:");
            $contents = file($fileName);
            $i = count($contents);
            while ($i >= 0) {
                if ($contents[$i][0] == $toDelete) {
                    $deletable = $i;
                    break;
                } else if ($i == 0) {
                    print_r('Error! ID not found.');
                }
                $i--;
            }
            unset($contents[$deletable]);
            $newContent = array_values($contents);
            $newContents = (array) $newContent;
            $fp = fopen($fileName, 'w');
            file_put_contents($fileName, $newContents);
            fclose($fp);
            break;

            //Print visitors list
        case 4:
            echo "List of visitors:\n";
            readFromFile();
            break;
    }
}