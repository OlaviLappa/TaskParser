<?

    Mainfunc();

	function Mainfunc() {
		
        $aliceCommand = $_POST['aliceCommand'];

        echo '<form method="post">
                <input type="text" name="aliceCommand" size="200">
                <p><input type="submit" name="button1" class="button" value="�������!">
                <br/>
                <br/>
              </from>';

		$basicWords = array(
			
			"�����",
            "�����",
            "������",
            "������",
            "�������",
            "�������",
            "������",
            "������"
		);

		$months = array(
				
			"�����",
            "������",
            "����",
            "�����",
            "��",
            "���",
            "���",
            "������",
            "�������",
            "������",
            "�����",
            "������"
		);

        if($aliceCommand != null || $aliceCommand != "") {

            if(array_key_exists('button1', $_POST)) {

                ParseCommand($aliceCommand, $basicWords, $months);
            }
        }
	}
    
    function ParseCommand($txt, $basicWords, $months) {
        
        $eventName = null;
        $creatingDate = null;
        $creatingTime = null;

        $itemsTxt = explode(" ", $txt);

        //��������� ��� ��� ��������� ��������

        ///���� � ������� ����� "����� ������..." �� �������
        for ($i = 0; $i < count($itemsTxt); $i++) {

            for ($j = 0; $j < count($basicWords); $j++) {
                
                if($itemsTxt[$i] == null || $itemsTxt[$i] == "") {
                    
                    continue;
                }

                if($itemsTxt[$i] == $basicWords[$j]) {

                    unset($itemsTxt[$i]);
                }
            }
        }

        $itemsTxt = array_values($itemsTxt);

        $valTime1 = 0;
        $valTime2 = 0;

        $parseValTime2;

        ///����� ���� � ������� � �������������� ���� �� string � DateTime
        for ($i = 0; $i < count($itemsTxt); $i++) {
        
            if(intval($itemsTxt[$i])) {

                $valTime1 = $itemsTxt[$i];
                
                if($itemsTxt[$i + 1] == "00") {

                    if($itemsTxt[$i - 1] == "��" || $itemsTxt[$i - 1] == "�") {

                        unset($itemsTxt[$i - 1]);
                    }
                    
                    $valTime2 = $itemsTxt[$i + 1];

                    unset($itemsTxt[$i]);
                    unset($itemsTxt[$i + 1]);

                    break;
                }

                if(intval($itemsTxt[$i + 1])) {

                    if($itemsTxt[$i - 1] == "��" || $itemsTxt[$i - 1] == "�") {

                        unset($itemsTxt[$i - 1]);
                    }
                    
                    $valTime2 = $itemsTxt[$i + 1];

                    unset($itemsTxt[$i]);
                    unset($itemsTxt[$i + 1]);

                    break;
                }

                if($valTime2 == 0) {

                   $valTime1 = 0; 
                }
            }

            if($i >= count($itemsTxt) && $valTime2 == 0) {
                
                $valTime2 = 0;
                break;
            }
        }

        $creatingTime = $valTime1.":".$valTime2;
        $itemsTxt = array_values($itemsTxt);

        ///�������/������/�����������
        for($i = 0; $i < count($itemsTxt); $i++) {

            if($itemsTxt[$i] == "�������") {

                if($itemsTxt[$i - 1] == "��") {

                    unset($itemsTxt[$i - 1]);
                }

                unset($itemsTxt[$i]);
                $creatingDate = date('Y-m-d');

                break;
            }

            if($itemsTxt[$i] == "������") {

                if($itemsTxt[$i - 1] == "��") {

                    unset($itemsTxt[$i - 1]);
                }

                unset($itemsTxt[$i]);

                $tomorrowDay = strtotime("+1 day");
                $creatingDate = date('Y-m-d', $tomorrowDay);

                break;
            }

            if($itemsTxt[$i] == "�����������") {

                if($itemsTxt[$i - 1] == "��") {

                    unset($itemsTxt[$i - 1]);
                }

                unset($itemsTxt[$i]);

                $dayAfterTomorrow = strtotime("+2 day");
                $creatingDate = date('Y-m-d', $dayAfterTomorrow);

                break;
            }
        }
        
        $itemsTxt = array_values($itemsTxt);
        
        $newDate = null;
        $numberOfMonth = 0;

        ///����� ������ � �����
        for($i = 0; $i < count($itemsTxt); $i++) {

            for($j = 0; $j < count($months); $j++) {

                if($itemsTxt[$i] == $months[$j].'�' || $itemsTxt[i] == $months[$j].'�') {

                    if(intval($itemsTxt[$i - 1])) {

                        $numberOfMonth = $itemsTxt[$i - 1];
                        $newDate = '2022-'.($j + 1).'-'.$numberOfMonth;

                        if($itemsTxt[$i - 2] == "��" || $itemsTxt[$i - 2] == "�") {

                            unset($itemsTxt[$i - 2]);
                        }

                        unset($itemsTxt[$i]);
                        unset($itemsTxt[$i - 1]);
                    }

                    break;
                }

                if($itemsTxt[$i] == $months[$j] || $itemsTxt[$i] == $months[$j].'�' ||
                    $itemsTxt[$i] == $months[$j].'�' || $itemsTxt[$i] == $months[$j].'�') {
                   
                    if(intval($itemsTxt[$i - 1])) {

                        $numberOfMonth = $itemsTxt[$i - 1];
                        $newDate = '2022-'.($j + 1).'-'.$numberOfMonth;

                        if($itemsTxt[$i - 2] == "��" || $itemsTxt[$i - 2] == "�") {

                            unset($itemsTxt[$i - 2]);
                        }

                        unset($itemsTxt[$i]);
                        unset($itemsTxt[$i - 1]);
                    }

                    break;
                }
            }
        }

        $itemsTxt = array_values($itemsTxt);

        ///����������� ��������� ������ � �������
        for($i = 0; $i < count($itemsTxt); $i++) {

            $eventName.=" ".$itemsTxt[$i];
        }

        ///���������� ���������� ��������:
        $isValid = true;

        if($creatingTime == "0:0" || ($newDate == null && $creatingDate == null)) {
            
            $isValid = false;
            echo "�� �� ������� ���� ��� ����� �������";
        }

        if($isValid == true) {
           
            echo $eventName.'</br>';

            //������� ����:
            if($newDate != null) {

                echo $newDate.'</br>';
            }

            else {

                echo $creatingDate.'</br>';
            }

            ///������� �����:
            if($valTime2 <= 9 && $valTime2 != "00" && $creatingTime != "0:0") {

                echo $creatingTime.'0'.'</br>';
            }

            else if($valTime2 > 9 || $valTime2 == "00" && $creatingTime != "0:0") {

                echo $creatingTime.'</br>';
            }   
        }
    }
?>