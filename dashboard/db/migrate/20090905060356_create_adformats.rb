class CreateAdformats < ActiveRecord::Migration
  def self.up
    create_table :adformats do |t|
      t.string :format_name
      t.integer :width
      t.integer :height

      t.timestamps
    end
  end

  def self.down
    drop_table :adformats
  end
end
