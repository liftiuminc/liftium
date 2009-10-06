class CreateFillsMinute < ActiveRecord::Migration
  def self.up
    execute "CREATE TABLE IF NOT EXISTS fills_minute (tag_id smallint unsigned not null, minute datetime, attempts int unsigned default 0, loads int unsigned default 0, rejects int unsigned default 0, PRIMARY KEY (tag_id, minute))"
  end

  def self.down
    drop_table :fills_minute
  end
end
