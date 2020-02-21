import unittest
import sys

class Test1(unittest.TestCase):
    def test_deepbugs(self):
        sys.path.append(".")
        from main import parse
        with open('tests/out.txt', 'r') as f:
            content = f.read()
            from main import parse
            res = parse(content)
            self.assertEqual(res['README.md'], 8)
            print(res)


if __name__ == '__main__':
    unittest.main()
