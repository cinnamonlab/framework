def run(cmd)
  cmd = "php phpunit.phar #{cmd}"
  puts "$ #{cmd}"
  system cmd
end

watch("^src/(.*).php")       { |m| run "tests/#{m[1]}Test.php" }
watch("^tests/(.*)Test.php") { |m| run "tests/#{m[1]}Test.php" }

# Ctrl-\
Signal.trap 'QUIT' do
  run "-cR tests"
end
