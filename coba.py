from firebase_admin import credentials, initialize_app, db

cred = credentials.Certificate("serviceAccountKey.json")
initialize_app(cred, {'databaseURL': 'https://sensi-17f27-default-rtdb.firebaseio.com'})

ref = db.reference('test')
ref.set({'hello': 'world'})
print("Firebase connection successful!")
