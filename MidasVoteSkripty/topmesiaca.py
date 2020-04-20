import urllib3
import json
import operator
import time
from datetime import datetime
import mysql.connector

#===================================================================
# Názov: Skript pre rátanie hlasov z roznych zdrojov z formatu JSON
# Autor: KillerXCoder (Peter Federl)
# E-Mail: peter.federl@gmail.com
#===================================================================


#Pripojenie na databazu

mydb = mysql.connector.connect(
  host="",
  user="",
  passwd="",
  database=""
)

t = datetime.now()
mesiac = t.month
rok = t.year
http = urllib3.PoolManager()

czechcraft = http.request('GET', 'https://czech-craft.eu/api/server/midascraft/votes/' + str(rok) +'/' + str(mesiac), headers={'Authorization': 'Bearer ===KLUC==='})
minecraftmp = http.request('GET', 'https://minecraft-mp.com/api/?object=servers&element=voters&key= ===KLUC===&month=current&format=json')
craftlist = http.request('GET', 'https://api.craftlist.org/v1/===KLUC===/votes/' + str(rok) +'/' + str(mesiac))

czechcraft_dict = json.loads(czechcraft.data.decode('UTF-8'))
minecraftmp_dict = json.loads(minecraftmp.data.decode('UTF-8'))
craftlist_dict = json.loads(craftlist.data.decode('UTF-8'))


i = 0
n = 0
a = 0
b = 0
h2 = 0
mena = []
mena3 = []
mena2 = {}
vysledny = {}
vysledny2 = {}
unikatny = []
unikatny2 = []

#==========
#Craftlist
#==========

for meno in craftlist_dict:
    mena3.append(meno['nickname'])

for x in mena3:
    if x not in unikatny:
        unikatny2.append(x)	

dlzka2 = len(mena3)
dlzka_unikatny2 = len(unikatny2)
while n < dlzka_unikatny2:
    while i < dlzka2:
        if mena3[i] == unikatny2[n]:
            a += 1
            i += 1
        else:
            i += 1
    if n != dlzka_unikatny2 - 1:
        vysledny2[unikatny2[n]] = a
        i = 0
        n += 1
        a = 0
    else:
        n += 1

		
i = 0
n = 0
a = 0

vysledny3 = list(reversed(sorted(vysledny2.items(), key=operator.itemgetter(1))))


#===========
#Czech-Craft
#===========

for meno in czechcraft_dict['data']:
    mena.append(meno['username'])

for x in mena:
    if x not in unikatny:
        unikatny.append(x)
 

dlzka = len(mena)
dlzka_unikatny = len(unikatny)
while n < dlzka_unikatny:
    while i < dlzka:
        if mena[i] == unikatny[n]:
            a += 1
            i += 1
        else:
            i += 1
    if n != dlzka_unikatny - 1:
        vysledny[unikatny[n]] = a
        i = 0
        n += 1
        a = 0
    else:
        n += 1
h2 = 0
h3 = 0
list1 = list(reversed(sorted(vysledny.items(), key=operator.itemgetter(1))))

#============
#Minecraft-MP
#============

for meno2 in minecraftmp_dict['voters']:
	mena2[meno2['nickname']] = meno2['votes']
	h2 += 1
list2 = list(reversed(sorted(mena2.items(), key=operator.itemgetter(1))))
h2 = 0
#=====================================
#Zratavanie Czech-Craft + Minecraft-MP
#=====================================
for x in list1:
	for n in list2:
		if(list1[h2][0] == list2[h3][0]):
			
			vysledny[list1[h2][0]] = int(list1[h2][1]) + int(list2[h3][1])
			
		h3 += 1
	h2 += 1
	h3 = 0

list1 = list(reversed(sorted(vysledny.items(), key=operator.itemgetter(1))))

h2 = 0
h3 = 0

#====================================================
#Zratavanie (Czech-Craft + Minecraft-MP ) + Craftlist
#====================================================

for x in list1:
	for n in vysledny3:
		if(list1[h2][0] == vysledny3[h3][0]):
			
			vysledny[list1[h2][0]] = int(list1[h2][1]) + int(vysledny3[h3][1])
			
			
		h3 += 1
	h2 += 1
	h3 = 0


vysledny2 = list(reversed(sorted(vysledny.items(), key=operator.itemgetter(1))))


#=================
#Zapis do databazy
#=================

mycursor = mydb.cursor()
mycursor.execute("TRUNCATE TABLE top10")

#Zapis iba prvych 10

while b < 10:
    sql = "INSERT INTO top10 (Poradie, Nick, Pocet) VALUES (%s, %s, %s)"
    val = (b+1, vysledny2[b][0], vysledny2[b][1])
    mycursor.execute(sql, val)
    b += 1
	
#Zapis vsetkych
mycursor.execute("TRUNCATE TABLE hlasy")
g = 0
for x in vysledny2:
   sql = "INSERT INTO hlasy (Poradie, Nick, Pocet) VALUES (%s, %s, %s)"
   val = (g+1, vysledny2[g][0], vysledny2[g][1])
   mycursor.execute(sql, val)
   g += 1

mydb.commit()

