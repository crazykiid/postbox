DB Info
**********
Database Name	: postbox_v1
Collections	: users, posts, following

Commands
**********
db.createCollection("users")
db.createCollection("posts")
db.createCollection("following")
db.users.createIndex({"username": 1}, {unique: true, name: "username"})
db.users.createIndex({"email": 1}, {unique: true, name: "email"})
db.following.createIndex({"master": 1, "follower" : 1}, {unique: true, name: "follower"})

Data
**********
Directory postbox_v1 has JSON files that you can import in your collections.