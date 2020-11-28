from sklearn.linear_model import LinearRegression
import statsmodels.api as sm
import pandas as pd

# --- PREPROCESSING DATA ---
# Compile Master Data (2018-2021 data, for each year having an estimate for specific home value and zip level)
# START WITH JUST 1 BEDROOM

df = pd.read_csv('data/zhvi_1br.csv')
df1 = pd.read_csv('data/zhvi.csv')
years_relevant = ['RegionName', '2017', '2018', '2019', '2020', '2021']
df = df[years_relevant]
df1 = df1[years_relevant]

# Rename columns
df.columns = df.columns.map(lambda x : x+'_br1' if x !='RegionName' else x)
df1.columns = df1.columns.map(lambda x : x+'_zip' if x !='RegionName' else x)

master_df = df.merge(df1, on='RegionName', how='outer')


# TRAIN THE MODEL
#train_df = master_df.drop(columns=['2021_zip', '2021_br1'])

train_df = pd.DataFrame({})
"""train_df['alpha'] = master_df['2019_br1'] * (master_df['2020_zip'] - master_df['2019_zip']) / master_df['2019_zip']
train_df['beta'] = (master_df['2020_zip'] - master_df['2019_zip']) / (master_df['2019_zip'] - master_df['2018_zip']) \
                   * (master_df['2019_br1'] - master_df['2018_br1'])
train_df['outcome'] = master_df['2020_br1'] - master_df['2019_br1']"""

train_df['alpha'] = master_df['2018_br1'] * (master_df['2019_zip'] - master_df['2018_zip']) / master_df['2018_zip']
train_df['beta'] = (master_df['2019_zip'] - master_df['2018_zip']) / (master_df['2018_zip'] - master_df['2017_zip']) \
                   * (master_df['2018_br1'] - master_df['2017_br1'])
train_df['outcome'] = master_df['2019_br1'] - master_df['2018_br1']
print(train_df)

trainX = train_df[['alpha', 'beta']]
trainy = train_df['outcome']

lg = sm.OLS(trainy, trainX, missing='drop')
results = lg.fit()
print(results.summary())

test_df = pd.DataFrame({})
test_df['alpha'] = master_df['2020_br1'] * (master_df['2021_zip'] - master_df['2020_zip']) / master_df['2020_zip']
test_df['beta'] = (master_df['2021_zip'] - master_df['2020_zip']) / (master_df['2020_zip'] - master_df['2019_zip']) \
                   * (master_df['2020_br1'] - master_df['2019_br1'])
test_df['outcome'] = master_df['2021_br1'] - master_df['2020_br1']

testX = test_df[['alpha', 'beta']]
testy = test_df['outcome']

print(testy + master_df['2020_br1'])
predictions = results.predict(testX) + master_df['2020_br1']
print(predictions)