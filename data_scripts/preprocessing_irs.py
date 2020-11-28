import pandas as pd

"""AGI_STUB dictionary
1 = $1 under $25,000
2 = $25,000 under $50,000
3 = $50,000 under $75,000
4 = $75,000 under $100,000
5 = $100,000 under $200,000
6 = $200,000 or more
"""
#agi_dic = {'1': 15000, '2': 37500, '3': 62500, '4': 87500, '5': 150000, '6': 500000}
agi_dic = {1: 20000, 2: 37500, 3: 62500, 4: 87500, 5: 150000, 6: 600000}

df = pd.read_csv('raw_data/17zpallagi.csv')
df = df[['zipcode', 'agi_stub', 'N1']]
df['agi_stub'] = df['agi_stub'].map(agi_dic)
df.to_csv('data/agi_2017.csv', index=False)

df1 = pd.read_csv('raw_data/18zpallagi.csv')
df1 = df1[['zipcode', 'agi_stub', 'N1']]
df1['agi_stub'] = df1['agi_stub'].map(agi_dic)
print(df1[df1['zipcode'] == 0])

df1.to_csv('data/agi_2018.csv', index=False)