class CreateAdFormats < ActiveRecord::Migration
  def self.up
    create_table :ad_formats do |t|
      t.string :ad_format_name
      t.string :size
      t.timestamps
    end

    add_index :ad_formats, :ad_format_name, {"unique" => true}
    add_index :ad_formats, :size, {"unique" => true}
  end
  
  def self.down
    drop_table :ad_formats
  end
end
