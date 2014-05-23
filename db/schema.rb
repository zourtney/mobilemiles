# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema.define(version: 20140523034832) do

  # These are extensions that must be enabled in order to support this database
  enable_extension "plpgsql"

  create_table "maintenance_records", force: true do |t|
    t.string   "type"
    t.string   "name"
    t.integer  "vehicle_id"
    t.integer  "user_id"
    t.integer  "mileage"
    t.datetime "completed_at"
    t.text     "comment"
    t.float    "gallons"
    t.float    "price_per_gallon"
    t.string   "grade"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.float    "price"
  end

  add_index "maintenance_records", ["user_id"], name: "index_maintenance_records_on_user_id", using: :btree
  add_index "maintenance_records", ["vehicle_id"], name: "index_maintenance_records_on_vehicle_id", using: :btree

  create_table "users", force: true do |t|
    t.string   "email"
    t.string   "password_digest"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "vehicles", force: true do |t|
    t.string   "name"
    t.string   "make"
    t.string   "model"
    t.integer  "year"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "user_id"
  end

  add_index "vehicles", ["user_id"], name: "index_vehicles_on_user_id", using: :btree

end
