import unittest
import sys
#import requests

class Test1(unittest.TestCase):
    def test_deepbugs(self):
        # sys.path.append(".")
        from volatility.main import parse
        with open('tests/out.txt', 'r') as f:
            content = f.read()
            res = parse(content)
            self.assertEqual(res['files']['README.md'], 8)


if __name__ == '__main__':
    unittest.main()
