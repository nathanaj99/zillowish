import pandas as pd
import numpy as np
from pyspark import SparkContext
#print(pd.set_option('display.max_columns', None))

def average_list(x):
    sum = 0
    num = 0
    for i in x:
        sum += int(i[0]) * float(i[1])
        num += float(i[1])
    return float(sum)/float(num)

file_path = 'data/agi_2017.csv'

sc = SparkContext('local[*]', 'tax')
lines = sc.textFile(file_path).map(lambda x: x.split(','))

# Filter out column names
header = lines.first()
lines = lines.filter(lambda x: x != header).filter(lambda x: x[0] != '0').map(lambda x: (x[0], (x[1], x[2])))

lines = lines.groupByKey().map(lambda x: (x[0], list(x[1]))).mapValues(lambda x: average_list(x))

lines = lines.collectAsMap()
df = pd.DataFrame(list(lines.items()), columns=['RegionName', 'AGI'])
df.to_csv('data/agi_2017.csv')