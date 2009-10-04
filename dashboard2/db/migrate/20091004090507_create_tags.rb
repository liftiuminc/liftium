class CreateTags < ActiveRecord::Migration
  def self.up
    create_table :tags do |t|
      t.string :tag_name
      t.references :network
      t.references :publisher
      t.references :adformat
      t.integer :value_in_cents
      t.boolean :enabled
      t.boolean :always_fill
      t.integer :sample_rate
      t.integer :tier
      t.integer :frequency_cap
      t.integer :rejection_time
      t.string :size
      t.text :tag
      t.timestamps
     
    end
  end
  
  def self.down
    drop_table :tags
  end
end
