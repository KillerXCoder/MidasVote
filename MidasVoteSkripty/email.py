import urllib3 
import smtplib
import json
import ssl
import operator
import time
from datetime import datetime
import mysql.connector

#===================================================================
# Názov: Skript pre zasielanie e-mailovych notifikacii pre hlasovanie
# Autor: KillerXCoder (Peter Federl)
# E-Mail: peter.federl@gmail.com
#===================================================================

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

mydb = mysql.connector.connect(
  host="",
  user="",
  passwd="",
  database=""
)






http = urllib3.PoolManager()





x = 0
y = 0
doplnanie_rozdiel = 0


	

	

def poslat_email(email, sprava):
	port = 465  
	smtp_server = ""
	sender_email = ""  
	receiver_email = email  
	password = ""

	context = ssl.create_default_context()
	with smtplib.SMTP_SSL(smtp_server, port, context=context) as server:
		server.login(sender_email, password)
		server.sendmail(sender_email, receiver_email, sprava)
		
mycursor = mydb.cursor()
mycursor.execute("SELECT * FROM midasvote")
vsetci = mycursor.fetchall()
	
for n in vsetci:
	print (n)
	cas = datetime.strptime(n[4], '%Y-%m-%d %H:%M:%S')
	message = """\
Subject: Upozornenie na moznost hlasovat za server


Ahoj, 

tento e-mail si obdrzal, pretoze si sa prihlasil na odoberanie notifikacii o moznosti hlasovat za server MidasCraft cez sluzbu Czech-Craft.eu. 
Oznamujeme ti, ze v tejto chvili mozes znovu hlasovat! Dakujeme za podporu serveru!

Link na hlasovanie: https://czech-craft.eu/server/midascraft/vote/?user=""" +  n[1] + """ 

Ak si neprajes dostavat dalsie tieto e-maily, je potrebne konatktovat nasu hernu podporu prostrednictvom Ticket systemu. Link: https://midascraft.sk/submit-ticket/

S pozdravom,

Team MidasCraft
	"""
	if (n[4] > time.strftime('%Y-%m-%d %H:%M:%S')):
		nick = http.request('GET', 'https://czech-craft.eu/api/server/midascraft/player/' + n[1] +'/next_vote', headers={'Authorization': 'Bearer 5901e500a982cd1a1cab7784cf687a879617319d8c2e0a2dcc8b5de333f80a3c'})
		nick_dict = json.loads(nick.data.decode('UTF-8'))
		print('https://czech-craft.eu/api/server/midascraft/player/' + n[1] +'/')
		if (time.strftime('%Y-%m-%d %H:%M:%S') >= nick_dict['next_vote']):
			if(str(n[3]) != str(nick_dict['next_vote'])):
				poslat_email(n[2], message)
				sql = "UPDATE midasvote SET poslane = '" + nick_dict['next_vote']  + "' WHERE nick = '" + n[1] + "'"
				mycursor.execute(sql)
				mydb.commit()
		x += 1
		y += 2
	else:
		sql = "DELETE FROM midasvote WHERE id = " + str(n[0])
		mycursor.execute(sql)
		mydb.commit()
		





