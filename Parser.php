<?

    Mainfunc();

	function Mainfunc() {
		
        $aliceCommand = $_POST['aliceCommand'];

        echo '<form method="post">
                <input type="text" name="aliceCommand" size="200">
                <p><input type="submit" name="button1" class="button" value="Парсинг!">
                <br/>
                <br/>
              </from>';

		$basicWords = array(
			
			"Алиса",
            "алиса",
            "Добавь",
            "добавь",
            "событие",
            "события",
            "задачу",
            "задачи"
		);

		$months = array(
				
			"январ",
            "феврал",
            "март",
            "апрел",
            "ма",
            "июн",
            "июл",
            "август",
            "сентябр",
            "октябр",
            "ноябр",
            "декабр"
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

        //прописать код для коррекции пробелов

        ///Ищем и удаляем слова "алиса добавь..." из команды
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

        ///Поиск даты в массиве и преобразование даты из string в DateTime
        for ($i = 0; $i < count($itemsTxt); $i++) {
        
            if(intval($itemsTxt[$i])) {

                $valTime1 = $itemsTxt[$i];
                
                if($itemsTxt[$i + 1] == "00") {

                    if($itemsTxt[$i - 1] == "на" || $itemsTxt[$i - 1] == "в") {

                        unset($itemsTxt[$i - 1]);
                    }
                    
                    $valTime2 = $itemsTxt[$i + 1];

                    unset($itemsTxt[$i]);
                    unset($itemsTxt[$i + 1]);

                    break;
                }

                if(intval($itemsTxt[$i + 1])) {

                    if($itemsTxt[$i - 1] == "на" || $itemsTxt[$i - 1] == "в") {

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

        ///сегодня/завтра/послезавтра
        for($i = 0; $i < count($itemsTxt); $i++) {

            if($itemsTxt[$i] == "сегодня") {

                if($itemsTxt[$i - 1] == "на") {

                    unset($itemsTxt[$i - 1]);
                }

                unset($itemsTxt[$i]);
                $creatingDate = date('Y-m-d');

                break;
            }

            if($itemsTxt[$i] == "завтра") {

                if($itemsTxt[$i - 1] == "на") {

                    unset($itemsTxt[$i - 1]);
                }

                unset($itemsTxt[$i]);

                $tomorrowDay = strtotime("+1 day");
                $creatingDate = date('Y-m-d', $tomorrowDay);

                break;
            }

            if($itemsTxt[$i] == "послезавтра") {

                if($itemsTxt[$i - 1] == "на") {

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

        ///поиск месяца и числа
        for($i = 0; $i < count($itemsTxt); $i++) {

            for($j = 0; $j < count($months); $j++) {

                if($itemsTxt[$i] == $months[$j].'я' || $itemsTxt[i] == $months[$j].'ь') {

                    if(intval($itemsTxt[$i - 1])) {

                        $numberOfMonth = $itemsTxt[$i - 1];
                        $newDate = '2022-'.($j + 1).'-'.$numberOfMonth;

                        if($itemsTxt[$i - 2] == "на" || $itemsTxt[$i - 2] == "в") {

                            unset($itemsTxt[$i - 2]);
                        }

                        unset($itemsTxt[$i]);
                        unset($itemsTxt[$i - 1]);
                    }

                    break;
                }

                if($itemsTxt[$i] == $months[$j] || $itemsTxt[$i] == $months[$j].'а' ||
                    $itemsTxt[$i] == $months[$j].'й' || $itemsTxt[$i] == $months[$j].'я') {
                   
                    if(intval($itemsTxt[$i - 1])) {

                        $numberOfMonth = $itemsTxt[$i - 1];
                        $newDate = '2022-'.($j + 1).'-'.$numberOfMonth;

                        if($itemsTxt[$i - 2] == "на" || $itemsTxt[$i - 2] == "в") {

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

        ///Определение контекста задачи в массиве
        for($i = 0; $i < count($itemsTxt); $i++) {

            $eventName.=" ".$itemsTxt[$i];
        }

        ///Отображаем результаты парсинга:
        $isValid = true;

        if($creatingTime == "0:0" || ($newDate == null && $creatingDate == null)) {
            
            $isValid = false;
            echo "Вы не указали дату или время события";
        }

        if($isValid == true) {
           
            echo $eventName.'</br>';

            //Выводим дату:
            if($newDate != null) {

                echo $newDate.'</br>';
            }

            else {

                echo $creatingDate.'</br>';
            }

            ///Выводим время:
            if($valTime2 <= 9 && $valTime2 != "00" && $creatingTime != "0:0") {

                echo $creatingTime.'0'.'</br>';
            }

            else if($valTime2 > 9 || $valTime2 == "00" && $creatingTime != "0:0") {

                echo $creatingTime.'</br>';
            }   
        }
    }
?>