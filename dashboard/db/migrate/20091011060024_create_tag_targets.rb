class CreateTagTargets < ActiveRecord::Migration
  def self.up
    create_table :tag_targets do |t|
      t.references :tag
      t.string :key_name
      t.string :key_value
    end
   
    add_index :tag_targets, [:tag_id, :key_name], {:unique => true }
  end
  
  def self.down
    drop_table :tag_targets
  end
end
