import java.awt.GraphicsEnvironment;

public class ListeAvailableFonts {
    public static void main(String[] args) {
        GraphicsEnvironment ge = GraphicsEnvironment.getLocalGraphicsEnvironment();
        String[] fontNames = ge.getAvailableFontFamilyNames();

        System.out.println("Fontes disponíveis na JVM:");
        for (String fontName : fontNames) {
            System.out.println(fontName);
        }
    }
}