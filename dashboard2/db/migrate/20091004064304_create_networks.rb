class CreateNetworks < ActiveRecord::Migration
  def self.up
    create_table :networks do |t|
      t.string :network_name
      t.string :website
      t.string :pay_type
      t.boolean :enabled
      t.boolean :supports_threshold
      t.boolean :default_always_fill
      t.boolean :us_only
      t.text :comments
      t.text :contact_info
      t.string :billing_info
      t.integer :brand_safety_level
      t.text :tag_template
      t.text :scraping_instructions
      t.timestamps
    end
  end
  
  def self.down
    drop_table :networks
  end
end
