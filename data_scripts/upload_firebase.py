import json
import requests
import csv

# General
def csv_to_json(file_path):
    years = [2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021]
    dic = {}
    with open(file_path) as csvf:
        csvReader = csv.DictReader(csvf)
        for rows in csvReader:
            years_dic = {}
            for i in years:
                if rows[str(i)] == '':
                    years_dic[i] = 0
                else:
                    years_dic[i] = float(rows[str(i)])
            dic[int(rows['RegionName'])] = years_dic

    return json.dumps(dic)


def to_firebase(file_path, url):
    output = csv_to_json(file_path)
    response = requests.put(url, output)

# NOTE: Anything below here has dummy 2021 data. Need to replace with the predictive model
# 1br
to_firebase('data/zhvi_1br.csv', 'https://dsci551-finalproject.firebaseio.com/1br.json')

# 2br
to_firebase('data/zhvi_2br.csv', 'https://dsci551-finalproject.firebaseio.com/2br.json')
# 3br
to_firebase('data/zhvi_3br.csv', 'https://dsci551-finalproject.firebaseio.com/3br.json')
# 4br
to_firebase('data/zhvi_4br.csv', 'https://dsci551-finalproject.firebaseio.com/4br.json')
# 5br+
to_firebase('data/zhvi_5br.csv', 'https://dsci551-finalproject.firebaseio.com/5br.json')
# condos/coops
to_firebase('data/zhvi_condo.csv', 'https://dsci551-finalproject.firebaseio.com/condo.json')