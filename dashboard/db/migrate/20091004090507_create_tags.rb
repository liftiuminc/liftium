class CreateTags < ActiveRecord::Migration
  def self.up
    create_table :tags do |t|
      t.string :tag_name
      t.references :network
      t.references :publisher
      t.decimal :value, {:precision => 3, :scale => 2 }
      t.boolean :enabled, :default=> true
      t.boolean :always_fill, :default=> false
      t.integer :sample_rate, {:limit => 1}
      t.integer :tier, {:limit => 1}
      t.integer :frequency_cap, {:limit => 1}
      t.integer :rejection_time, {:limit => 1}
      t.string :size
      t.text :tag
      t.timestamps
     
    end
    add_index(:tags, :network_id);
    add_index(:tags, :publisher_id);
  end
  
  def self.down
    drop_table :tags
  end
end
