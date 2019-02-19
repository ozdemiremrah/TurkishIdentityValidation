<?php
namespace OzdemirEmrah\TurkishIdentityValidation;
/**
 * @author Emrah ÖZDEMİR
 * @date 2019-02-19
 * @url <https://github.com/OzdemirEmrah>
 */
 
class TurkishIdentityValidation {

    private $serviceUrl = "https://tckimlik.nvi.gov.tr/Service/";
    
    /*
        @param int $identity_id
        @return boolean
    */
    public function IdValidation($identity_id){
        if(strlen($identity_id) != 11)
            return false;

        if(!ctype_digit($identity_id))
            return false;
        
        $splited_identity = preg_split('//', $identity_id, -1, PREG_SPLIT_NO_EMPTY);
        
        if($splited_identity[0] == 0)
            return false;
            
        $splited_odd_sum   = $splited_identity[0] + $splited_identity[2] + $splited_identity[4] + $splited_identity[6] + $splited_identity[8];;
        $splited_even_sum  = $splited_identity[1] + $splited_identity[3] + $splited_identity[5] + $splited_identity[7];
        $calculate_10 = ($splited_odd_sum*7-$splited_even_sum)%10;
        $_11th = ($splited_odd_sum+$splited_even_sum+$splited_identity[9])%10;

        if($calculate_10 != $splited_identity[9] || $_11th != $splited_identity[10])
            return false;
        
        return true;
    }

    /* Validation via http://tckimlik.nvi.gov.tr 
            // It's only working with Identity,Name,Surname,Birth Year
            <-
                Ex: 
                $data = [
                    "identity" => "12345678901"
                    "name" => "Emrah"
                    "surname" => "Özdemir"
                    "year" => 1995
                ];
           -> 
    
        @param Array $data
        @return boolean    
    */

    public function IdentityValidation($data){

        $post_data = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
					<TCKimlikNo>'.$data['identity'].'</TCKimlikNo>
					<Ad>'.$this->tr_strtoupper($data['name']).'</Ad>
					<Soyad>'.$this->tr_strtoupper($data['surname']).'</Soyad>
					<DogumYili>'.$data['year'].'</DogumYili>
				</TCKimlikNoDogrula>
			</soap:Body>
        </soap:Envelope>';
        

        $data = [
            "endpoint" => "KPSPublic.asmx",
            "post" => $post_data,
            "header" => [
                'POST /Service/KPSPublic.asmx HTTP/1.1',
                'Host: tckimlik.nvi.gov.tr',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
                'Content-Length: '.strlen($post_data)
                ]
        ];

        return $this->request($data);
    }

    /* document validation via http://tckimlik.nvi.gov.tr 
            <-
                Ex for old document
                $data = [
                    "type" => 1, // old identity document
                    "identity" => "12345678901",
                    "name" => "Emrah",
                    "surname" => "Özdemir",
                    "day" => "01",
                    "month" => "01",
                    "year" => "1995",
                    "document_serial" => "A01",
                    "document_no" => "415496"
                ];

                or new card

                $data = [
                    "type" => 2, // identity card
                    "identity" => "12345678901",
                    "name" => "Emrah",
                    "surname" => "Özdemir",
                    "day" => "01",
                    "month" => "01",
                    "year" => "1995",
                    "document_serial" => "S06D41236"
                ];
            
           -> 
    
        @param Array $data
        @return boolean    
    */

    public function IdentityDocumentValidation($data){

        if($data["type"] == 1){
            $post_data = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <KisiVeCuzdanDogrula  xmlns="http://tckimlik.nvi.gov.tr/WS">
                        <TCKimlikNo>'.$data['identity'].'</TCKimlikNo>
                        <Ad>'.$this->tr_strtoupper($data['name']).'</Ad>
                        <Soyad>'.$this->tr_strtoupper($data['surname']).'</Soyad>
                        <DogumGun>'.$data['day'].'</DogumGun>
                        <DogumAy>'.$data['month'].'</DogumAy>
                        <DogumYil>'.$data['year'].'</DogumYil>
                        <CuzdanSeri>'.$data['document_serial'].'</CuzdanSeri>
                        <CuzdanNo>'.$data['document_no'].'</CuzdanNo>
                    </KisiVeCuzdanDogrula >
                </soap:Body>
            </soap:Envelope>';
        }else if($data["type"] == 2){
            $post_data = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <KisiVeCuzdanDogrula  xmlns="http://tckimlik.nvi.gov.tr/WS">
                        <TCKimlikNo>'.$data['identity'].'</TCKimlikNo>
                        <Ad>'.$this->tr_strtoupper($data['name']).'</Ad>
                        <Soyad>'.$this->tr_strtoupper($data['surname']).'</Soyad>
                        <DogumGun>'.$data['day'].'</DogumGun>
                        <DogumAy>'.$data['month'].'</DogumAy>
                        <DogumYil>'.$data['year'].'</DogumYil>
                        <TCKKSeriNo>'.$data['document_serial'].'</TCKKSeriNo>
                    </KisiVeCuzdanDogrula >
                </soap:Body>
            </soap:Envelope>';
        }

        $data = [
            "endpoint" => "KPSPublicV2.asmx",
            "post" => $post_data,
            "header" => [
                'POST /Service/KPSPublicV2.asmx HTTP/1.1',
                'Host: tckimlik.nvi.gov.tr',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/KisiVeCuzdanDogrula"',
                'Content-Length: '.strlen($post_data)
                ]
        ];

        return $this->request($data);
    }

    /* curl request 
        @param array $data
        @return boolean
    */
    protected function request($data){
        $ch = curl_init();
		$options = array(
			CURLOPT_URL				=> $this->serviceUrl.$data["endpoint"],
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> $data["post"],
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_HEADER			=> false,
			CURLOPT_HTTPHEADER		=> $data["header"],
		);
		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);
		curl_close($ch);
		return (strip_tags($response) === 'true') ? true : false;
    } 

    /* 
        Uppercase all letters - Turkish characters are supported
        @param string $text
        @return string
    */
    public function  tr_strtoupper($text)
    {
        $search=["ç","i","ı","ğ","ö","ş","ü"];
        $replace=["Ç","İ","I","Ğ","Ö","Ş","Ü"];
        $text=str_replace($search,$replace,$text);
        return strtoupper($text);
    }
    
}