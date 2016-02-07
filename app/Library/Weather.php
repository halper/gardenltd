<?php
namespace App\Library {
    class Weather
    {
        private $appid = '570240fc9688bc26dba535b40820e8cf';
        private $json = '';
        private $day;
        private $city;

        /**
         * Weather constructor.
         * @param $day
         */
        public function __construct($day = 0, $city = 'ankara')
        {
            $this->day = $day;
            $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç");//turkish letters
            $english = array("i", "g", "u", "s", "o", "c");//english cooridinators letters

            $this->city = str_replace($turkish, $english, mb_strtolower($city, 'utf-8'));
        }


        public function getWeather()
        {
            $url = "http://api.openweathermap.org/data/2.5/forecast/daily?q=$this->city&mode=json&units=metric&lang=tr&cnt=2&appid=$this->appid";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_ENCODING ,"");
            $data = curl_exec($curl);
            curl_close($curl);
            $data = (preg_replace('/\n/i','', $data));
//            $content = file_get_contents($url);
            $this->json = json_decode($data, true);
//            dd($this->json);
        }

        public function getMin()
        {
            return $this->getTemp()["min"];
        }

        public function getMax()
        {
            return $this->getTemp()["max"];
        }

        public function getPressure()
        {
            return $this->getList($this->day)["pressure"];
        }

        public function getHumidity()
        {
            return $this->getList($this->day)["humidity"];
        }

        public function getWind()
        {
            return $this->getList($this->day)["speed"];
        }

        public function getDirection()
        {
            return $this->getList($this->day)["deg"];
        }

        private function getList($day = 0)
        {
            $this->getWeather();
            return $this->json["list"][$day];
        }

        private function getTemp()
        {
            return $this->getList($this->day)["temp"];
        }

        public function getDescription()
        {
            return $this->getList($this->day)["weather"][0]["description"];
        }

    }
}