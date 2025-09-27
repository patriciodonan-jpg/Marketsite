#!/usr/bin/env python3
import os
from datetime import datetime
from pymongo import MongoClient
from collections import Counter

MONGO_URI = os.getenv('MONGO_URI', 'mongodb://localhost:27017')
DB = os.getenv('MONGO_DB', 'marketsite')
COLL = os.getenv('MONGO_COLLECTION', 'contact_submissions')

client = MongoClient(MONGO_URI)
db = client[DB]
coll = db[COLL]

docs = coll.find({}, {'created_at': 1})

def to_py_datetime(bson_dt):
    try:
        return bson_dt.to_datetime()
    except Exception:
        return bson_dt

dates = []
for d in docs:
    dt = d.get('created_at')
    if dt:
        dt = to_py_datetime(dt)
        dates.append(dt.date())
    else:
        dates.append(None)

counts = Counter(d for d in dates if d is not None)
print('Submissions per date:')
for date, cnt in sorted(counts.items()):
    print(f"{date.isoformat()}: {cnt}")
print(f"Total submissions: {sum(counts.values())}")
