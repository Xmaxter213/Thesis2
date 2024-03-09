// HEART RATE CODE
#include <Wire.h>
#include <WiFi.h>
#include "MAX30105.h"
#include "heartRate.h"
#include "ESP32Servo.h"
#include <WiFiUdp.h>
#include <NTPClient.h>
MAX30105 particleSensor;

const byte RATE_SIZE = 4;
byte rates[RATE_SIZE];
byte rateSpot = 0;
long lastBeat = 0;
float beatsPerMinute;
int beatAvg;
String BPM;

double avered = 0;
double aveir = 0;
double sumirrms = 0;
double sumredrms = 0;

double SpO2 = 0;
double ESpO2 = 90.0;
double FSpO2 = 0.7;
double frate = 0.95;
int i = 0;
int Num = 30;
#define FINGER_ON 7000
#define MINIMUM_SPO2 87.0

int Tonepin = 4;

WiFiUDP ntpUDP;
// initialized to a time offset of 8 hours (Philippines time zone)
NTPClient timeClient(ntpUDP, "pool.ntp.org", 28800); // UTC+8 (8 hours * 3600 seconds per hour)

int currentHour;
int currentMinute;
int currentSecond;
int hour;
String period; // AM/PM indicator

TaskHandle_t Task1;
TaskHandle_t Task2;

int flexval1 = 0; // Variable to store flex sensor 1 reading
int flexval2 = 0; // Variable to store flex sensor 2 reading

const int LED_PIN = 27;   // LED connected to pin 5
const int BUTTON_PIN = 26; // Button connected to pin 4

// LCD CODE
#include <U8g2lib.h>

U8G2_SSD1306_128X64_NONAME_F_HW_I2C u8g2(U8G2_R0, /* reset=*/ U8X8_PIN_NONE);
String aaa = "JASON";
int u8g_init = 0;
unsigned long u8LCDmilli;

#define Flex1 35
#define Flex2 32

const char* ssid = "ZTE_2.4G_Rvv5KV";
const char* password = "3CbGK2Fb";

// 'battery_full', 30x10px
const unsigned char epd_bitmap_battery_full [] PROGMEM = {
	0xfc, 0xff, 0xff, 0x07, 0x02, 0x00, 0x00, 0x08, 0xf9, 0xff, 0xff, 0x11, 0xfd, 0xff, 0xff, 0x33, 
	0xfd, 0xff, 0xff, 0x37, 0xfd, 0xff, 0xff, 0x37, 0xfd, 0xff, 0xff, 0x33, 0xf9, 0xff, 0xff, 0x11, 
	0x02, 0x00, 0x00, 0x08, 0xfc, 0xff, 0xff, 0x07
};


// 'Empty_battery', 30x10px
const unsigned char epd_bitmap_Empty_battery [] PROGMEM = {
	0xfc, 0xff, 0xff, 0x07, 0x02, 0x00, 0x00, 0x08, 0x01, 0x00, 0x00, 0x10, 0x01, 0x00, 0x00, 0x30, 
	0x01, 0x00, 0x00, 0x30, 0x01, 0x00, 0x00, 0x30, 0x01, 0x00, 0x00, 0x30, 0x01, 0x00, 0x00, 0x10, 
	0x02, 0x00, 0x00, 0x08, 0xfc, 0xff, 0xff, 0x07
};

// 'heartbeat', 30x20px
// 'HeartRate', 23x20px
const unsigned char epd_bitmap_HeartRate [] PROGMEM = {
	0xe0, 0x83, 0x0f, 0xf8, 0xc7, 0x1f, 0x18, 0xee, 0x38, 0x0c, 0x3c, 0x70, 0x0c, 0x38, 0x60, 0x0e, 
	0x18, 0x60, 0x0e, 0x00, 0x60, 0x0c, 0xc1, 0x60, 0x8c, 0xe3, 0x61, 0xc0, 0xf3, 0x71, 0xe0, 0xb6, 
	0x33, 0x7f, 0x3e, 0x3f, 0x7f, 0x1c, 0x1e, 0x00, 0x1c, 0x0c, 0x80, 0x00, 0x0e, 0xc0, 0x01, 0x07, 
	0x80, 0xc3, 0x03, 0x00, 0xef, 0x00, 0x00, 0x7c, 0x00, 0x00, 0x38, 0x00
};

// 'Oxygen (1)', 23x20px
const unsigned char epd_bitmap_Oxygen__1_ [] PROGMEM = {
	0xc0, 0xff, 0x01, 0x30, 0x00, 0x06, 0x08, 0x00, 0x08, 0x04, 0x00, 0x10, 0x02, 0xf0, 0x20, 0x02, 
	0x08, 0x21, 0xc1, 0x09, 0x42, 0x21, 0x02, 0x42, 0x21, 0x02, 0x42, 0x21, 0x02, 0x41, 0x21, 0xf2, 
	0x40, 0x21, 0x0a, 0x40, 0x21, 0x0a, 0x40, 0x21, 0x0a, 0x40, 0x22, 0xf2, 0x21, 0xc2, 0x01, 0x20, 
	0x04, 0x00, 0x10, 0x08, 0x00, 0x08, 0x30, 0x00, 0x06, 0xc0, 0xff, 0x01
};

WiFiClient client;
int status = WL_IDLE_STATUS;

int HTTP_PORT = 80;
String HTTP_METHOD = "GET";
char HOST_NAME[] = "192.168.1.8"; // IPv4
String PATH_NAME = "/Thesis2/arduino/assistance_Send.php";

bool buttonPressed = false;
unsigned long buttonPressStartTime = 0;
bool assistanceChanged = false;

int assistance = 0;
int devID = 2;
float bpm2 = 0;
float spo2 = 0;
int batpercent = 0;
String problems = "";

//VOLTAGE CODE
float adc_voltage = 0.0;
float in_voltage = 0.0;
float R1 = 1000000.0;
float R2 = 1000000.0; 
float ref_voltage = 3.7;
int adc_value = 0;
float battery_voltage = 0.0;
float battery_percent = 0.0;
int bat_percent = 0;
int Valperc;

void setup() {
  delay(1000);
  Serial.begin(115200);
  Wire.begin();

  Serial.begin(115200);
  delay(1000);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("\nConnecting");

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    ReadConnect();
    delay(100);
  }

  Serial.println("\nConnected to the WiFi network");
  Serial.print("Local ESP32 IP: ");
  Serial.println(WiFi.localIP());

  pinMode(LED_PIN, OUTPUT);
  pinMode(BUTTON_PIN, INPUT_PULLUP);

  pinMode(Flex1, INPUT); // Configure Flex1 pin as input
  pinMode(Flex2, INPUT); // Configure Flex2 pin as input

  timeClient.begin();

  if (!particleSensor.begin(Wire, I2C_SPEED_FAST)) {
    Serial.println("MAX30102 Not connected");
    while (1);
  }
  byte ledBrightness = 0x7F;
  byte sampleAverage = 4;
  byte ledMode = 2;
  int sampleRate = 800;
  int pulseWidth = 215;
  int adcRange = 16384;
  particleSensor.setup(ledBrightness, sampleAverage, ledMode, sampleRate, pulseWidth, adcRange);
  particleSensor.enableDIETEMPRDY();

  particleSensor.setPulseAmplitudeRed(0x0A);
  particleSensor.setPulseAmplitudeGreen(0);

  xTaskCreatePinnedToCore(Task1code, "Task1", 20000, NULL, 0, &Task1, 0);
  delay(2000);
  xTaskCreatePinnedToCore(Task2code, "Task2", 20000, NULL, 1, &Task2, 1);
  delay(500);
}

void Task1code(void* pvParameters) {
  unsigned long lastSendTime = 0;
  unsigned long buttonpress = 0;
  bool buttonPressed = false;
  bool abpmnormal = false;
  bool spo2mnormal = false;
  bool batteryinitial = false;
  Serial.print(F("Task1 running on core 0\n"));
  for (;;) {
    // if (millis() - lastSendTime >= 10000) {
    //   Send_database();
    //   lastSendTime = millis(); // Update last send time
    // }
    LocalTime();
    u8LCD_OUT();
    flexval1 = analogRead(Flex1);
    flexval2 = analogRead(Flex2);

    int buttonState = digitalRead(BUTTON_PIN);

    if (buttonState == LOW && !buttonPressed) {
        buttonPressed = true;
        buttonPressStartTime = millis(); 
    } else if (buttonState == HIGH && buttonPressed) {
        buttonPressed = false;
        if (millis() - buttonPressStartTime < 5000) {
          
            if (flexval1 > 3400) {
                assistance = 1;
                Send_assistance_database();
                assistance = 0;
                buttonPressed = false; 
                digitalWrite(LED_PIN, LOW); 
            } else if (flexval2 > 3300) {
                assistance = 2;
                Send_assistance_database();
                assistance = 0;
                buttonPressed = false; // Reset the buttonPressed flag
                digitalWrite(LED_PIN, LOW); // Turn off the LED
            }
        }
    }
    
    if (millis() - buttonPressStartTime >= 5000) {
        digitalWrite(LED_PIN, LOW);
    } else {
        digitalWrite(LED_PIN, HIGH); 
        buttonPressed = true;
    }

    if (abpmnormal == false && bpm2 > 70)
    {
      abpmnormal = true;
    }

    if (abpmnormal == true && problems != "  CRITICAL BPM")
    {
      if (bpm2 > 120 || bpm2 < 61)
      {
        assistance = 3;
        digitalWrite(LED_PIN, HIGH);
        Send_assistance_database();
        assistance = 0;
        digitalWrite(LED_PIN, LOW);
        abpmnormal = false;
      }
    }

    if (spo2mnormal == false && ESpO2 > 94)
    {
      spo2mnormal = true;
    }

    if (spo2mnormal == true && problems != "  LOW OXYGEN")
    {
      if (ESpO2 < 89)
      {         
        assistance = 4;
        digitalWrite(LED_PIN, HIGH);
        Send_assistance_database();
        assistance = 0;
        digitalWrite(LED_PIN, LOW);
        spo2mnormal = false;
      }
    }
    if(batteryinitial == false && battery_percent > 20)
    {
      batteryinitial= true;
    }

    if(batteryinitial == true && problems != "    Low Battery")
    {
      if(battery_percent < 15)
      {
        digitalWrite(LED_PIN, HIGH);
        assistance = 5;
        Send_assistance_database();
        assistance = 0;
        digitalWrite(LED_PIN, LOW);
      }
    }
    

    

    delay(100);
    delayMicroseconds(10);
  }
}

void Task2code(void* pvParameters) {
  Serial.print(F("Task2 running on core 1\n"));
  for (;;) {
    Check_Heart();
    ReadVoltage();
    delayMicroseconds(10);
  }
}

void loop() {}

void ReadConnect(){
    if (u8g_init == 0) {
    u8g2.begin();
    u8g_init = 1;
  }

  String Line2 = "  CONNECTING";
  String Line3 = "...................";

  if (u8LCDmilli < millis()) {
    u8LCDmilli = millis() + 1013;
    u8g2.firstPage();
    do {
      u8g2.setFont(u8g2_font_TimesNewPixel_tr);
      u8g2.drawStr(10, 26, Line2.c_str());
      u8g2.drawStr(35, 47, Line3.c_str());
    } while (u8g2.nextPage());
  }
}

void ReadVoltage(){
  adc_value = analogRead(34);
  adc_voltage  = (adc_value * ref_voltage) / 4096.0; 
  in_voltage = adc_voltage / (R2/(R1+R2));
  battery_percent = ((in_voltage - 3.3) / (4.2 - 3.3)) * 100.0;
  bat_percent = battery_percent;
  Valperc= map(in_voltage*100,330,420,0,100);
 
  }

void u8LCD_OUT() {
  if (u8g_init == 0) {
    u8g2.begin();
    u8g_init = 1;
  }
  int fill_with = map(battery_percent,0 ,100,2,25);

  String formattedMinute = String(currentMinute);
  if (currentMinute < 10) {
    formattedMinute = "0" + formattedMinute;
  }

  String Line1 = String(hour) + ":" + formattedMinute + period;
  String Line2 = String(beatAvg);
  String Line3 = String(ESpO2);//ESpO2
  String Line4 = problems;
  if (u8LCDmilli < millis()) {
    u8LCDmilli = millis() + 1013;
    u8g2.firstPage();
    do {
      u8g2.setFont(u8g2_font_TimesNewPixel_tr);
      u8g2.drawStr(1, 9, Line1.c_str());
      u8g2.drawStr(35, 26, Line2.c_str());
      u8g2.drawStr(35, 47, Line3.c_str());
      u8g2.drawStr(8, 63, Line4.c_str());
      u8g2.drawXBMP(95,1,30,10, epd_bitmap_Empty_battery);
      u8g2.drawXBMP(2,12,23,20, epd_bitmap_HeartRate);
      u8g2.drawXBMP(2,34,23,20, epd_bitmap_Oxygen__1_);
      u8g2.drawRBox(98,2,fill_with,8,1);
    } while (u8g2.nextPage());
  }
}

// void Send_database() {
//   PATH_NAME = "/arduino/sensorNano.php";
//   if (client.connect(HOST_NAME, HTTP_PORT)) {
//     Serial.println("Connected to server");
//     client.print(HTTP_METHOD + " " + PATH_NAME + "?devID=");
//     client.print(devID, 1);
//     client.print("&bpm=");
//     client.print(bpm2, 2);
//     client.print("&batteryPercent=");
//     client.print(battery_percent, 2);
//     client.println(" HTTP/1.1");
//     client.println("Host: " + String(HOST_NAME));
//     client.println("Connection: close");
//     client.println();
    
//     // Read response from server
//     while (client.connected()) {
//       if (client.available()) {
//         String line = client.readStringUntil('\n');
//         if (line.startsWith("HTTP/1.1 200 OK")) {
//           Serial.println("Received successful response from server");
//           // Continue with your ESP32 code here
//           // Example: You may set a flag to indicate successful transmission
//           // or perform other actions.
//           break; // Exit the loop since we've received the status we were waiting for
//         }
//       }
//     }
    
//     client.stop(); // Close the connection
//     Serial.println("disconnected");
//   } else {
//     Serial.println("Connection failed");
//   }
// }


void LocalTime()
{
  timeClient.update();

  // Store the current time components
  currentHour = timeClient.getHours();
  currentMinute = timeClient.getMinutes();
  currentSecond = timeClient.getSeconds();

  // Determine AM/PM based on current hour
  period = (currentHour < 12) ? "am" : "pm";

  hour = currentHour % 12;
  if (hour == 0) {
    hour = 12; // 12 AM (midnight)
  }

}


void Send_assistance_database() {
  PATH_NAME = "/Thesis2/arduino/assistance_Send.php";
  if (client.connect(HOST_NAME, HTTP_PORT)) {
    Serial.println("Connected to server");
    client.print(HTTP_METHOD + " " + PATH_NAME + "?devID=");
    client.print(devID);
    client.print("&assistance=");
    client.print(assistance);
    client.print("&bpm=");
    client.print(bpm2, 2);
    client.print("&spo2=");
    client.print(spo2, 2);
    client.print("&batteryPercent=");
    client.print(bat_percent);
    client.println(" HTTP/1.1");
    client.println("Host: " + String(HOST_NAME));
    client.println("Connection: close");
    client.println();

    while (client.connected()) {
      if (client.available()) {
        String line = client.readStringUntil('\n');
        if (line.startsWith("HTTP/1.1 200 OK")) {
          Serial.println("Received successful response from server");
          if(assistance == 1)
          {
            problems = "         ADL";
          } else if(assistance == 2)
          {
            problems = "    IMMEDIATE";
          } else if(assistance == 3)
          {
            problems = "  CRITICAL BPM";
          } else if(assistance == 4)
          {
            problems = "  LOW OXYGEN";
          } else if(assistance == 5)
          {
            problems = "    Low Battery";
          }
          // Continue with your ESP32 code here
          // Example: You may set a flag to indicate successful transmission
          // or perform other actions.
          break; // Exit the loop since we've received the status we were waiting for
        }
      }
    }
    
    client.stop();
    Serial.println();
    Serial.println("disconnected");
  } else {
    Serial.println("Connection failed");
    problems = "Connection Failed" + String(assistance);
  }
}



void Check_Heart() {

  long irValue = particleSensor.getIR();
  if (irValue > FINGER_ON ) {
    if (checkForBeat(irValue) == true) {
      long delta = millis() - lastBeat;
      lastBeat = millis();
      beatsPerMinute = 60 / (delta / 1000.0);
      if (beatsPerMinute < 255 && beatsPerMinute > 20) {
        rates[rateSpot++] = (byte)beatsPerMinute;
        rateSpot %= RATE_SIZE;
        beatAvg = 0;
        for (byte x = 0 ; x < RATE_SIZE ; x++) beatAvg += rates[x];
        beatAvg /= RATE_SIZE;
      }
    }

    uint32_t ir, red ;
    double fred, fir;
    particleSensor.check();
    if (particleSensor.available()) {
      i++;
      ir = particleSensor.getFIFOIR();
      red = particleSensor.getFIFORed();
      fir = (double)ir;
      fred = (double)red;
      aveir = aveir * frate + (double)ir * (1.0 - frate);
      avered = avered * frate + (double)red * (1.0 - frate);
      sumirrms += (fir - aveir) * (fir - aveir);
      sumredrms += (fred - avered) * (fred - avered);

      if ((i % Num) == 0) {
        double R = (sqrt(sumirrms) / aveir) / (sqrt(sumredrms) / avered);
        SpO2 = -23.3 * (R - 0.4) + 100;
        ESpO2 = FSpO2 * ESpO2 + (1.0 - FSpO2) * SpO2;
        if (ESpO2 <= MINIMUM_SPO2) ESpO2 = MINIMUM_SPO2;
        if (ESpO2 > 100) ESpO2 = 99.9;
        sumredrms = 0.0; sumirrms = 0.0; SpO2 = 0;
        i = 0;
      }
      particleSensor.nextSample();
    }

    bpm2 = beatAvg;
    spo2 = ESpO2;

  }
  else {
    for (byte rx = 0 ; rx < RATE_SIZE ; rx++) rates[rx] = 0;
    beatAvg = 0; rateSpot = 0; lastBeat = 0;
    avered = 0; aveir = 0; sumirrms = 0; sumredrms = 0;
    SpO2 = 0; ESpO2 = 90.0;
  }
}
