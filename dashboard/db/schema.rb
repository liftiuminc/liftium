# This file is auto-generated from the current state of the database. Instead of editing this file, 
# please use the migrations feature of Active Record to incrementally modify your database, and
# then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your database schema. If you need
# to create the application database on another system, you should be using db:schema:load, not running
# all the migrations from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 20090904215518) do

  create_table "networks", :force => true do |t|
    t.string   "network_name"
    t.string   "pay_type"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.boolean  "enabled",            :default => true
    t.boolean  "supports_threshold", :default => false
    t.boolean  "always_fill",        :default => true
  end

  create_table "publishers", :force => true do |t|
    t.string   "publisher_name"
    t.string   "website"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "tags", :force => true do |t|
    t.string  "tag_name"
    t.integer "network_id"
    t.integer "publisher_id"
    t.decimal "value",                       :precision => 3, :scale => 2
    t.boolean "enabled",                                                   :default => true
    t.boolean "always_fill",                                               :default => true
    t.integer "sample_rate",    :limit => 3
    t.integer "tier",           :limit => 3
    t.integer "frequency_cap",  :limit => 3
    t.integer "rejection_cap",  :limit => 3
    t.integer "rejection_time", :limit => 3
    t.text    "tag"
  end

end
