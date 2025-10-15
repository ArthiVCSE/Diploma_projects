#include <ESP32Servo.h>
#include <ThingSpeak.h>
#include <WiFi.h>

//thingspeak
WiFiClient client;
long myChannelNumber =   2617702;
const char * myWriteAPIKey = "N578CUEMVQEH0W1X";
int statusCode;

//wifi
char ssid[] = "Wokwi-GUEST";
char pass[] = "";

//servo motor
#define SERVO_PIN1 19
#define SERVO_PIN2 18
Servo s1;
Servo s2;

//ultrasonic
int trig=14;
int echo=12;
float d;

void setup() 
{
  ThingSpeak.begin(client);//thingspeak

  Serial.begin(115200);

  WiFi.mode(WIFI_STA);//wifi
  WiFi.begin(ssid, pass);

  //servo pins
  s1.attach(SERVO_PIN1); 
  s2.attach(SERVO_PIN2); 

  //ultra pins
  pinMode(trig, OUTPUT);
  pinMode(echo, INPUT);
  
  //led and buzzer pins
  pinMode(25, OUTPUT);//green
  pinMode(26, OUTPUT);//red
  pinMode(27, OUTPUT);//buzzer
}

int Ultrasonic()
{
  digitalWrite(trig,LOW);
  delay(10);
  digitalWrite(trig,HIGH);
  delay(100);
  digitalWrite(trig,LOW);
  d=pulseIn(echo,HIGH);
  return (d*0.034/2)+1;
  delay(1000);
}

void loop() 
{
  //wifi
  if(WiFi.status() == WL_CONNECTED)
  {
    Serial.println("Connected");
    delay(500);
  }
  else
  {
    Serial.println("Not Connected");
  }

  //ultrasonic
  int dis=Ultrasonic();
  Serial.println("The Train is at the distance of "+String(dis)+"cm");
  if(dis <= 300)
  {
    digitalWrite(25, LOW);
    digitalWrite(26, HIGH);
    tone(27,1500);
    delay(2000);
    noTone(27);
    Serial.println("The gate is now closed");
    for (int pos = 0; pos <= 90; pos += 1)
    { 
      s1.write(180);
      s2.write(0);
      delay(15);
    }
  }
  else
  {
    digitalWrite(25, HIGH);
    digitalWrite(26, LOW);
    noTone(27);
    Serial.println("The gate is now opened");
     for (int pos = 90; pos >= 0; pos -= 1) 
    {
      s1.write(90);
      s2.write(90);
      delay(15);
    }
  }

  //thingspeak
  ThingSpeak.setField(1, dis);
  statusCode = ThingSpeak.writeFields(myChannelNumber,myWriteAPIKey);
  if(statusCode == 200)
  { 
  Serial.println("Channel updated successfully.");
  }
  else 
  {
  Serial.println("Problem Writing data. HTTP error code :" +String(statusCode));
  }
  Serial.println("---------------------------------------------");
  delay(500);
}
