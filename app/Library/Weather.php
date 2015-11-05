<?php
namespace App\library {
    class Weather
    {
        private $appid = '570240fc9688bc26dba535b40820e8cf';
        private $json = '';

        public function getWeather()
        {
            $url = "http://api.openweathermap.org/data/2.5/forecast/daily?q=ankara&mode=json&units=metric&lang=tr&cnt=1&appid=$this->appid";
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
            return $this->getList()["pressure"];
        }

        public function getHumidity()
        {
            return $this->getList()["humidity"];
        }

        public function getWind()
        {
            return $this->getList()["speed"];
        }

        private function getList()
        {
            $this->getWeather();
            return $this->json["list"][0];
        }

        private function getTemp()
        {
            return $this->getList()["temp"];
        }

        public function getDescription(){
            return $this->getList()["weather"][0]["description"];
        }

    }
}