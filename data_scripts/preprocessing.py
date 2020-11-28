import pandas as pd
import numpy as np
from pyspark import SparkContext

# --- CLEAN ZIP DATASET ---
# REFER HERE https://www.zillow.com/research/zestimate-forecast-methodology/
def preprocess_zhvi_past(file_path):
    zip_code = pd.read_csv(file_path)

    # only want 2010-2020 data
    list_years = '|'.join([str(int(i)) for i in np.linspace(2010, 2020, 11)])

    column_years = [i for i in list(zip_code.columns) if '2010' in i or '2011' in i or '2012' in i or '2013' in i or '2014' in i
                    or '2015' in i or '2016' in i or '2017' in i or '2018' in i or '2019' in i or '2020' in i]
    column_years.insert(0, "RegionName")
    column_years.insert(1, "State")
    column_years.insert(1, "City")

    zip_code = zip_code[column_years]

    # For each year, take the mean of ZHVI
    agg = pd.DataFrame({})
    agg[['RegionName', 'City', 'State']] = zip_code[['RegionName', 'City', 'State']]

    year = 2010
    start = 1
    while year < 2020:
        col = zip_code.iloc[:, start:start+12]
        agg[str(year)] = col.mean(axis=1)
        start += 12
        year += 1
    col = zip_code.iloc[:, start:]
    agg[str(year)] = col.mean(axis=1)

    return agg

    #w = open('data/zip_cleaned.csv', 'w')
    #zip_code.to_csv('data/zip_cleaned.csv', index=False, header=True)


def combine_2021(zhvi):
    """
    :param zhvi: dataframe to merge 2021 data with
    :return: merged dataframe with 2021 prediction
    NOTE: prediction only has 30379 rows while timeseries dataset has 30415, so there is a discrepancy
    """
    zip_future = pd.read_csv('raw_data/zhvi_prediction.csv')
    zip_future = zip_future[zip_future['Region'] == 'Zip'][['RegionName', 'ForecastYoYPctChange']]
    zip_future['RegionName'] = zip_future['RegionName'].astype(int)

    combined = zhvi.merge(zip_future, on='RegionName', how='outer')

    combined['2021'] = combined['2020'] * (1 + combined['ForecastYoYPctChange']/100)
    combined = combined.drop(columns=['ForecastYoYPctChange'])

    return combined

def zhvi_main():
    files = ['zhvi.csv', 'zhvi_1br.csv', 'zhvi_2br.csv', 'zhvi_3br.csv', 'zhvi_4br.csv', 'zhvi_5br.csv', 'zhvi_condo.csv']
    for file in files:
        file_path = 'raw_data/' + file
        zhvi = preprocess_zhvi_past(file_path)
        combined = combine_2021(zhvi)
        combined.to_csv('data/' + 'zip_' + file, index=False)

def group_by():
    files = ['zhvi.csv', 'zhvi_1br.csv', 'zhvi_2br.csv', 'zhvi_3br.csv', 'zhvi_4br.csv', 'zhvi_5br.csv',
             'zhvi_condo.csv']

    for file in files:
        file_path = 'data/zip_' + file
        df = pd.read_csv(file_path)
        state = df.iloc[:, 2:]
        state = state.groupby('State').mean()
        states = {"AL": "Alabama", "AK": "Alaska", "AZ": "Arizona", "AR": "Arkansas", "CA": "California",
                  "CO": "Colorado", "CT": "Connecticut", "DE": "Delaware", "FL": "Florida", "GA": "Georgia",
                  "HI": "Hawaii", "ID": "Idaho", "IL": "Illinois", "IN": "Indiana", "IA": "Iowa", "KS": "Kansas",
                  "KY": "Kentucky", "LA": "Louisiana", "ME": "Maine", "MD": "Maryland", "MA": "Massachusetts",
                  "MI": "Michigan", "MN": "Minnesota", "MS": "Mississippi", "MO": "Missouri", "MT": "Montana",
                  "NE": "Nebraska", "NV": "Nevada", "NH": "New Hampshire", "NJ": "New Jersey", "NM": "New Mexico",
                  "NY": "New York", "NC": "North Carolina", "ND": "North Dakota", "OH": "Ohio", "OK": "Oklahoma",
                  "OR": "Oregon", "PA": "Pennsylvania", "RI": "Rhode Island", "SC": "South Carolina",
                  "SD": "South Dakota", "TN": "Tennessee", "TX": "Texas", "UT": "Utah", "VT": "Vermont",
                  "VA": "Virginia", "WA": "Washington", "WV": "West Virginia", "WI": "Wisconsin", "WY": "Wyoming"}

        state = state.reset_index()
        col = state['State'].replace(states)
        state.insert(1, 'State_Name', col)
        state.to_csv('data/state_' + file, index=False)

        city = df.iloc[:, 1:].groupby(['City', 'State']).mean()
        city = city.reset_index().reset_index()
        city.to_csv('data/city_' + file, index=False)

        buffer = city.iloc[:, :3]
        df = df.merge(buffer, right_on=['City', 'State'], left_on=['City', 'State'], how='left')
        df['City'] = df['index']
        df = df.drop(columns=['index']).dropna(subset=['City']).astype({"City": int})

        df.to_csv('data/zip_' + file, index=False)

def process_agi(in_file, out_file):
    """AGI_STUB dictionary
    1 = $1 under $25,000
    2 = $25,000 under $50,000
    3 = $50,000 under $75,000
    4 = $75,000 under $100,000
    5 = $100,000 under $200,000
    6 = $200,000 or more
    """
    agi_dic = {1: 20000, 2: 37500, 3: 62500, 4: 87500, 5: 150000, 6: 600000}

    df = pd.read_csv(in_file)
    df = df[['zipcode', 'agi_stub', 'N1']]
    df['agi_stub'] = df['agi_stub'].map(agi_dic)
    df.to_csv(out_file, index=False)

def average_list(x):
    sum = 0
    num = 0
    for i in x:
        sum += int(i[0]) * float(i[1])
        num += float(i[1])
    return float(sum)/float(num)

def spark_process_agi(sc, file_path):
    lines = sc.textFile(file_path).map(lambda x: x.split(','))

    # Filter out column names
    header = lines.first()
    lines = lines.filter(lambda x: x != header).filter(lambda x: x[0] != '0').map(lambda x: (x[0], (x[1], x[2])))

    lines = lines.groupByKey().map(lambda x: (x[0], list(x[1]))).mapValues(lambda x: average_list(x))

    lines = lines.collectAsMap()
    df = pd.DataFrame(list(lines.items()), columns=['RegionName', 'AGI'])
    df.to_csv(file_path, index=False)


def agi_main():
    process_agi('raw_data/18zpallagi.csv', 'data/agi_2018.csv')
    sc = SparkContext('local[*]', 'tax')
    spark_process_agi(sc, 'data/agi_2018.csv')
    process_agi('raw_data/17zpallagi.csv', 'data/agi_2017.csv')
    spark_process_agi(sc, 'data/agi_2017.csv')

def combine_zhvi_agi(zhvi_path, agi_2017, agi_2018):
    df = pd.read_csv(agi_2017)
    df1 = pd.read_csv(agi_2018)
    combine = df.merge(df1, on='RegionName', how='inner')
    combine = combine.rename(columns={'AGI_x': '2017', 'AGI_y': '2018'})
    combine['%Change'] = (combine['2018'] - combine['2017'])/combine['2017']
    combine = combine.drop(columns=['2017', '2018'])

    zhvi = pd.read_csv(zhvi_path)
    zhvi = zhvi.merge(combine, on='RegionName', how='left')
    zhvi.to_csv(zhvi_path, index=False)


#combine_zhvi_agi('data/zhvi.csv', 'data/agi_2017.csv', 'data/agi_2018.csv')