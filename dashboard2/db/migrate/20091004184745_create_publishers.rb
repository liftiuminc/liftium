class CreatePublishers < ActiveRecord::Migration
  def self.up
    create_table :publishers do |t|
      t.string :site_name
      t.string :site_url
      t.integer :brand_safety_level
      t.integer :hoptime
      t.timestamps
    end
  end
  
  def self.down
    drop_table :publishers
  end
end
