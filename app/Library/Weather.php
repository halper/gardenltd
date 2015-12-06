<?php
namespace App\Library {
    class Weather
    {
        private $appid = '570240fc9688bc26dba535b40820e8cf';
        private $json = '';
        private $day;

        /**
         * Weather constructor.
         * @param $day
         */
        public function __construct($day = 0)
        {
            $this->day = $day;
        }


        public function getWeather()
        {
            $url = "http://api.openweathermap.org/data/2.5/forecast/daily?q=ankara&mode=json&units=metric&lang=tr&cnt=2&appid=$this->appid";
            $content = file_get_contents($url);
            /*$ch =  curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);*/
            $this->json = json_decode($content, true);
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