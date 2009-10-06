class CreatePublishers < ActiveRecord::Migration
  def self.up
    create_table :publishers do |t|
      t.string :site_name
      t.string :site_url
      t.integer :brand_safety_level
      t.integer :hoptime
      t.timestamps
    end
    add_index :publishers, :site_name, {:unique => true }
  end
  
  def self.down
    drop_table :publishers
  end
end
