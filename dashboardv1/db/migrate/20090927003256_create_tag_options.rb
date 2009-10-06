class CreateTagOptions < ActiveRecord::Migration
  def self.up
    create_table :tag_options do |t|
      t.string :option_name
      t.string :option_value

      t.references :tag
    end
    add_index(:tag_options, [:tag_id, :option_name], :unique => true);
  end

  def self.down
    drop_table :tag_options
  end
end
