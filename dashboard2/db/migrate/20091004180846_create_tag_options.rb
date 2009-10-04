class CreateTagOptions < ActiveRecord::Migration
  def self.up
    create_table :tag_options do |t|
      t.references :tag
      t.string :option_name
      t.string :option_value
    end
  end
  
  def self.down
    drop_table :tag_options
  end
end
